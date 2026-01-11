<?php

namespace App\Console\Commands;

use App\Models\Bag;
use App\Jobs\SendPushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendBagTravelDateReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bags:send-travel-reminders {--days-before=1 : Number of days before travel date to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send travel date reminders to users before their bag travel date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $daysBefore = (int) $this->option('days-before');
        
        $this->info("Checking for bags with travel date in {$daysBefore} day(s)...");

        try {
            // Calculate target date (daysBefore days from now)
            $targetDateStart = Carbon::now()->addDays($daysBefore)->startOfDay();
            $targetDateEnd = Carbon::now()->addDays($daysBefore)->endOfDay();

            // Get bags with departure_date matching target date
            // We check for bags that have departure_date exactly on the target date
            $bags = Bag::whereDate('departure_date', '=', $targetDateStart->format('Y-m-d'))
                ->whereNotNull('departure_date')
                ->whereHas('user', function ($query) {
                    $query->whereNotNull('fcm_token');
                })
                ->with(['user', 'items'])
                ->get();

            if ($bags->isEmpty()) {
                $this->info('No bags found with travel date in the specified period.');
                return Command::SUCCESS;
            }

            $this->info("Found {$bags->count()} bag(s) to process.");

            $notificationsSent = 0;
            $notificationsFailed = 0;

            foreach ($bags as $bag) {
                try {
                    // Skip if user doesn't have FCM token
                    if (!$bag->user || !$bag->user->fcm_token) {
                        $this->line("Skipping Bag #{$bag->id}: User has no FCM token.");
                        continue;
                    }

                    // Get user's preferred language
                    $userLang = $bag->user->preferred_language ?? 'ar';

                    // Format travel date
                    $travelDate = Carbon::parse($bag->departure_date);
                    $daysUntilTravel = Carbon::now()->diffInDays($travelDate, false);
                    
                    // Prepare notification messages
                    if ($userLang === 'en') {
                        $title = '✈️ Travel Reminder';
                        
                        if ($daysUntilTravel == 0) {
                            $body = "Your travel is today! Don't forget to complete your bag '{$bag->name}' before departure.";
                        } elseif ($daysUntilTravel == 1) {
                            $body = "Your travel is tomorrow ({$travelDate->format('M d, Y')})! Complete your bag '{$bag->name}' before departure.";
                        } else {
                            $body = "Your travel is in {$daysUntilTravel} days ({$travelDate->format('M d, Y')}). Don't forget to complete your bag '{$bag->name}'.";
                        }
                    } else {
                        $title = '✈️ تذكير السفر';
                        
                        // Format date in Arabic
                        $dateStr = $travelDate->format('Y-m-d');
                        $dateParts = explode('-', $dateStr);
                        $formattedDate = "{$dateParts[2]}/{$dateParts[1]}/{$dateParts[0]}";
                        
                        if ($daysUntilTravel == 0) {
                            $body = "سفرك اليوم! لا تنسى إكمال حقيبتك '{$bag->name}' قبل المغادرة.";
                        } elseif ($daysUntilTravel == 1) {
                            $body = "سفرك غداً ({$formattedDate})! أكمل حقيبتك '{$bag->name}' قبل المغادرة.";
                        } else {
                            $body = "سفرك بعد {$daysUntilTravel} يوم ({$formattedDate}). لا تنسى إكمال حقيبتك '{$bag->name}'.";
                        }
                    }

                    // Prepare notification data
                    $data = [
                        'type' => 'bag_travel_reminder',
                        'bag_id' => (string) $bag->id,
                        'travel_date' => $travelDate->format('Y-m-d'),
                        'days_until_travel' => (string) $daysUntilTravel,
                        'bag_name' => $bag->name,
                        'language' => $userLang,
                        'timestamp' => now()->toIso8601String(),
                    ];

                    // Send notification
                    SendPushNotification::dispatchSync(
                        $bag->user->fcm_token,
                        $title,
                        $body,
                        $data
                    );

                    $notificationsSent++;
                    $this->info("✓ Sent travel reminder for Bag #{$bag->id} ({$bag->name}) - User: {$bag->user->name} - Travel Date: {$travelDate->format('Y-m-d')}");

                    Log::info('Bag travel date reminder sent', [
                        'bag_id' => $bag->id,
                        'user_id' => $bag->user->id,
                        'travel_date' => $travelDate->format('Y-m-d'),
                        'days_until_travel' => $daysUntilTravel,
                    ]);

                } catch (\Exception $e) {
                    $notificationsFailed++;
                    $this->error("✗ Failed to send reminder for Bag #{$bag->id}: {$e->getMessage()}");
                    Log::error('Bag travel date reminder failed', [
                        'bag_id' => $bag->id,
                        'user_id' => $bag->user->id ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            // Summary
            $this->newLine();
            $this->info("=== Summary ===");
            $this->info("Bags processed: {$bags->count()}");
            $this->info("Notifications sent: {$notificationsSent}");
            
            if ($notificationsFailed > 0) {
                $this->error("Notifications failed: {$notificationsFailed}");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Command failed: {$e->getMessage()}");
            Log::error('Send Bag Travel Date Reminders Command Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }
}

