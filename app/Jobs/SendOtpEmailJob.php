<?php

namespace App\Jobs;

use App\Traits\SendMailTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SendMailTrait;

    /**
     * عدد المحاولات في حالة الفشل
     */
    public $tries = 3;

    /**
     * timeout للـ job (ثواني)
     */
    public $timeout = 30;

    public string $email;
    public string $subject;
    public string $body;

    public function __construct($email, $subject, $body)
    {
        $this->email = $email;
        $this->subject = $subject;
        $this->body = $body;
    }

    public function handle(): void
    {
        try {
            $result = $this->sendEmail($this->email, $this->subject, $this->body);

            if ($result['status'] !== 200) {
                // Throw exception to trigger retry
                throw new \Exception($result['error'] ?? 'Failed to send email');
            }
        } catch (\Exception $e) {
            throw $e; // Re-throw to trigger queue retry mechanism
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Job failed after all retries
    }
}
