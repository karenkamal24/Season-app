<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\Reminder;
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

                $reminderTime = Carbon::createFromFormat('H:i', $timeString, $timezone);

                $shouldSend = false;
                $toleranceMinutes = 1;

                Log::info("Checking reminder #{$reminder->id} ({$reminder->recurrence}) for user #{$reminder->user_id}");

                switch ($reminder->recurrence) {
                    case 'once':
                        if (($currentDateTime->isSameDay($reminderDateTime) ||
                            $currentDateTime->greaterThan($reminderDateTime)) &&
                            !$reminder->last_sent_at) {

                            $diff = abs($currentDateTime->diffInMinutes($reminderDateTime));

                            if ($diff <= $toleranceMinutes || $currentDateTime->greaterThanOrEqualTo($reminderDateTime)) {
                                $shouldSend = true;
                                $reminder->status = 'completed';
                                Log::info("Sending reminder #{$reminder->id}");
                            }
                        }
                        break;

                    case 'daily':
                        $targetTime = $currentDateTime->copy()->setTimeFromTimeString($timeString);
                        $diff = abs($currentDateTime->diffInMinutes($targetTime));

                        if ($diff <= $toleranceMinutes) {
                            $lastSent = $reminder->last_sent_at ? Carbon::parse($reminder->last_sent_at, $timezone) : null;
                            if (!$lastSent || !$lastSent->isToday()) {
                                $shouldSend = true;
                            }
                        }
                        break;

                    case 'weekly':
                        $targetTime = $currentDateTime->copy()->setTimeFromTimeString($timeString);
                        $diff = abs($currentDateTime->diffInMinutes($targetTime));

                        if ($currentDateTime->dayOfWeek === $reminderDateTime->dayOfWeek && $diff <= $toleranceMinutes) {
                            $lastSent = $reminder->last_sent_at ? Carbon::parse($reminder->last_sent_at, $timezone) : null;
                            if (!$lastSent || !$lastSent->isSameWeek($currentDateTime)) {
                                $shouldSend = true;
                            }
                        }
                        break;

                    case 'monthly':
                        $targetTime = $currentDateTime->copy()->setTimeFromTimeString($timeString);
                        $diff = abs($currentDateTime->diffInMinutes($targetTime));

                        if ($currentDateTime->day === $reminderDateTime->day && $diff <= $toleranceMinutes) {
                            $lastSent = $reminder->last_sent_at ? Carbon::parse($reminder->last_sent_at, $timezone) : null;
                            if (!$lastSent || !$lastSent->isSameMonth($currentDateTime)) {
                                $shouldSend = true;
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

                    $sentCount++;
                    Log::info("Sent reminder #{$reminder->id} to user #{$reminder->user_id}");
                    $this->info("Sent reminder #{$reminder->id} to user #{$reminder->user_id}");
                }

            } catch (\Throwable $e) {
                $errors++;
                Log::error('Failed to send reminder', [
                    'reminder_id' => $reminder->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to send reminder #{$reminder->id}: {$e->getMessage()}");
            }
        }

        $this->info("Processed {$sentCount} reminders. Errors: {$errors}");
        Log::info("Finished sending reminders. Sent={$sentCount}, Errors={$errors}");
        return Command::SUCCESS;
    }
}
