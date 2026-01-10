<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reminder;
use App\Models\TravelBag;
use App\Jobs\SendPushNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send scheduled reminders as push notifications';

    public function handle()
    {
        $this->info('Checking for reminders to send...');
        Log::info('Starting reminders:send command');

        $now = Carbon::now('Africa/Cairo');
        $sentCount = 0;
        $errors = 0;

        $reminders = Reminder::where('status', 'active')
            ->with('user')
            ->get();

        Log::info('Total active reminders fetched: ' . $reminders->count());

        foreach ($reminders as $reminder) {
            try {
                if (!$reminder->user || !$reminder->user->fcm_token) {
                    Log::info("Skipped reminder #{$reminder->id}, user has no FCM token.");
                    continue;
                }

                $timezone = $reminder->timezone ?? 'Africa/Cairo';
                $currentDateTime = Carbon::now($timezone);

                $reminderDate = Carbon::parse($reminder->date, $timezone);

                $timeString = $reminder->time;
                if (is_string($timeString)) {
                    if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $timeString, $matches)) {
                        $timeString = $matches[1] . ':' . $matches[2];
                    }
                } elseif (is_object($timeString) && method_exists($timeString, 'format')) {
                    $timeString = $timeString->format('H:i');
                }

                $reminderDateTime = Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $reminderDate->format('Y-m-d') . ' ' . $timeString,
                    $timezone
                );

                $shouldSend = false;

                Log::info("Checking reminder #{$reminder->id} ({$reminder->recurrence}) for user #{$reminder->user_id}");

                switch ($reminder->recurrence) {
                    case 'once':
                        // تبعت لو الوقت الحالي >= وقت التذكير ومتبعتش قبل كده
                        if ($currentDateTime->greaterThanOrEqualTo($reminderDateTime) && !$reminder->last_sent_at) {
                            // لو التذكير مربوط بشنطة سفر، تحقق من حالة الشنطة
                            if ($reminder->travel_bag_id) {
                                $travelBag = \App\Models\TravelBag::find($reminder->travel_bag_id);
                                if ($travelBag) {
                                    if (!$travelBag->is_ready) {
                                        Log::info("Skipping reminder #{$reminder->id} - travel bag #{$travelBag->id} is not ready yet (status: {$travelBag->status}, current_weight: {$travelBag->current_weight}, max_weight: {$travelBag->max_weight})");
                                        continue 2;
                                    }
                                    Log::info("Travel bag #{$travelBag->id} is ready (status: {$travelBag->status}), will send reminder #{$reminder->id}");
                                }
                            }

                            $shouldSend = true;
                            $reminder->status = 'completed';
                            Log::info("Sending reminder #{$reminder->id}");
                        }
                        break;

                    case 'daily':
                        // تبعت لو الوقت الحالي >= وقت التذكير النهارده ومتبعتش النهارده
                        $targetTime = $currentDateTime->copy()->startOfDay()->setTimeFromTimeString($timeString);

                        if ($currentDateTime->greaterThanOrEqualTo($targetTime)) {
                            $lastSent = $reminder->last_sent_at ? Carbon::parse($reminder->last_sent_at, $timezone) : null;
                            if (!$lastSent || !$lastSent->isToday($timezone)) {
                                $shouldSend = true;
                                Log::info("Sending daily reminder #{$reminder->id}");
                            }
                        }
                        break;

                    case 'weekly':
                        // تبعت لو اليوم صح والوقت >= وقت التذكير ومتبعتش الأسبوع ده
                        if ($currentDateTime->dayOfWeek === $reminderDateTime->dayOfWeek) {
                            $targetTime = $currentDateTime->copy()->startOfDay()->setTimeFromTimeString($timeString);

                            if ($currentDateTime->greaterThanOrEqualTo($targetTime)) {
                                $lastSent = $reminder->last_sent_at ? Carbon::parse($reminder->last_sent_at, $timezone) : null;
                                if (!$lastSent || !$lastSent->isSameWeek($currentDateTime)) {
                                    $shouldSend = true;
                                    Log::info("Sending weekly reminder #{$reminder->id}");
                                }
                            }
                        }
                        break;

                    case 'monthly':
                        // تبعت لو اليوم من الشهر صح والوقت >= وقت التذكير ومتبعتش الشهر ده
                        if ($currentDateTime->day === $reminderDateTime->day) {
                            $targetTime = $currentDateTime->copy()->startOfDay()->setTimeFromTimeString($timeString);

                            if ($currentDateTime->greaterThanOrEqualTo($targetTime)) {
                                $lastSent = $reminder->last_sent_at ? Carbon::parse($reminder->last_sent_at, $timezone) : null;
                                if (!$lastSent || !$lastSent->isSameMonth($currentDateTime)) {
                                    $shouldSend = true;
                                    Log::info("Sending monthly reminder #{$reminder->id}");
                                }
                            }
                        }
                        break;
                }

                if ($shouldSend) {
                    $userLang = $reminder->user->preferred_language ?? 'ar';

                    $title = $reminder->title;
                    $notes = $reminder->notes;
                    $reminderDateFormatted = $reminderDate->format('Y-m-d');
                    $reminderTimeFormatted = $timeString;

                    $bodyPrefix = $userLang === 'en' ? 'Reminder: ' : 'تذكير: ';
                    $body = $notes ?: $bodyPrefix . "{$reminderDateFormatted} {$reminderTimeFormatted}";

                    $data = [
                        'type' => 'reminder',
                        'reminder_id' => (string) $reminder->id,
                        'title' => $title,
                        'date' => $reminderDateFormatted,
                        'time' => $reminderTimeFormatted,
                        'recurrence' => $reminder->recurrence,
                        'language' => $userLang,
                        'timestamp' => now('Africa/Cairo')->toIso8601String(),
                    ];

                    SendPushNotification::dispatchSync(
                        $reminder->user->fcm_token,
                        $title,
                        $body,
                        $data
                    );

                    $reminder->last_sent_at = now('Africa/Cairo');
                    $reminder->save();

                    // لو التذكير مربوط بشنطة سفر (سفر فعلي) امسح الشنطة بعد إرسال التذكير
                    if ($reminder->recurrence === 'once' && $reminder->travel_bag_id) {
                        $bag = TravelBag::find($reminder->travel_bag_id);
                        if ($bag) {
                            $bag->bagItems()->delete();
                            $bag->delete();
                            Log::info("Deleted travel bag #{$bag->id} after sending its trip reminder.");
                        }
                    }

                    $sentCount++;
                    Log::info("Sent reminder #{$reminder->id} to user #{$reminder->user_id}");
                    $this->info("Sent reminder #{$reminder->id} to user #{$reminder->user_id}");
                }

            } catch (\Throwable $e) {
                $errors++;
                $errorMessage = $e->getMessage();

                if (isset($reminder) && $reminder->recurrence === 'once' && $reminder->status === 'completed') {
                    $reminder->save();
                    Log::warning("Failed to send 'once' reminder #{$reminder->id}, but marking as completed to prevent retry", [
                        'reminder_id' => $reminder->id,
                        'error' => $errorMessage,
                    ]);
                }

                if (isset($reminder) && $reminder->user &&
                    (str_contains($errorMessage, 'UNREGISTERED') ||
                     (str_contains($errorMessage, '404') && str_contains($errorMessage, 'not found')))) {
                    Log::warning("Invalid FCM token for user #{$reminder->user_id}, removing token", [
                        'user_id' => $reminder->user_id,
                        'reminder_id' => $reminder->id,
                    ]);
                    $reminder->user->update(['fcm_token' => null]);
                }

                $reminderId = isset($reminder) ? $reminder->id : 'unknown';
                Log::error('Failed to send reminder', [
                    'reminder_id' => $reminderId !== 'unknown' ? $reminderId : null,
                    'error' => $errorMessage,
                ]);
                $this->error("Failed to send reminder #{$reminderId}: {$errorMessage}");
            }
        }

        $this->info("Processed {$sentCount} reminders. Errors: {$errors}");
        Log::info("Finished sending reminders. Sent={$sentCount}, Errors={$errors}");
        return Command::SUCCESS;
    }
}
