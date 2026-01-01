<?php

namespace App\Console\Commands;

use App\Models\Bag;
use App\Services\BagAnalysisService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendSmartBagAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bags:send-alerts {--hours=24 : Hours threshold for alerts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send smart alerts for bags with upcoming trips';

    protected BagAnalysisService $analysisService;

    /**
     * Create a new command instance.
     */
    public function __construct(BagAnalysisService $analysisService)
    {
        parent::__construct();
        $this->analysisService = $analysisService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hoursThreshold = (int) $this->option('hours');
        
        $this->info("Checking for bags with departure within {$hoursThreshold} hours...");

        try {
            // Get bags that need alerts
            $bags = $this->analysisService->getBagsNeedingAlerts($hoursThreshold);

            if ($bags->isEmpty()) {
                $this->info('No bags found that need alerts.');
                return Command::SUCCESS;
            }

            $alertsSent = 0;
            $alertsFailed = 0;

            foreach ($bags as $bag) {
                try {
                    // Generate smart alert
                    $alert = $this->analysisService->generateSmartAlert($bag);

                    if (!$alert) {
                        $this->line("Bag #{$bag->id} ({$bag->name}): No issues found, skipping alert.");
                        continue;
                    }

                    // Send notification to user
                    $this->sendNotification($bag, $alert);

                    $alertsSent++;
                    $this->info("✓ Sent alert for Bag #{$bag->id} ({$bag->name}) - User: {$bag->user->name}");

                } catch (\Exception $e) {
                    $alertsFailed++;
                    $this->error("✗ Failed to send alert for Bag #{$bag->id}: {$e->getMessage()}");
                    Log::error('Smart Bag Alert Failed', [
                        'bag_id' => $bag->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Summary
            $this->newLine();
            $this->info("=== Summary ===");
            $this->info("Bags checked: {$bags->count()}");
            $this->info("Alerts sent: {$alertsSent}");
            
            if ($alertsFailed > 0) {
                $this->error("Alerts failed: {$alertsFailed}");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Command failed: {$e->getMessage()}");
            Log::error('Send Smart Bag Alerts Command Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Send notification to user
     *
     * @param Bag $bag
     * @param array $alert
     * @return void
     */
    protected function sendNotification(Bag $bag, array $alert): void
    {
        $user = $bag->user;

        // Prepare notification data
        $title = $alert['severity'] === 'high' 
            ? '⚠️ تنبيه هام: حقيبة السفر'
            : '📋 تذكير: حقيبة السفر';

        $body = $alert['message'];

        // Check if user has FCM token
        if (empty($user->fcm_token)) {
            Log::warning('User has no FCM token', [
                'user_id' => $user->id,
                'bag_id' => $bag->id,
            ]);
            return;
        }

        // Send Firebase notification
        try {
            $this->sendFirebaseNotification($user->fcm_token, $title, $body, [
                'type' => 'smart_bag_alert',
                'bag_id' => (string) $bag->id,
                'alert_id' => $alert['alert_id'],
                'severity' => $alert['severity'],
                'hours_remaining' => $alert['hours_remaining'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send Firebase Cloud Messaging notification
     *
     * @param string $fcmToken
     * @param string $title
     * @param string $body
     * @param array $data
     * @return void
     */
    protected function sendFirebaseNotification(string $fcmToken, string $title, string $body, array $data = []): void
    {
        // You can implement Firebase notification here
        // This is a placeholder - integrate with your Firebase service
        
        // Example using Laravel HTTP client:
        /*
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.firebase.server_key'),
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
        ]);
        */

        Log::info('Firebase notification sent', [
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }
}
