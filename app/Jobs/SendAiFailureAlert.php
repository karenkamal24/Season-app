<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SendAiFailureAlert implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;
    public array $backoff = [30];

    public function __construct(
        public string $message,
        public string $exceptionType,
        public string $language,
    ) {}

    public function handle(): void
    {
        $url = config('smart_bag_ai.alert_webhook_url');
        if (!$url) {
            return;
        }

        Http::connectTimeout(2)->timeout(3)->post($url, [
            'text' => "AI Service Failed: {$this->message} ({$this->exceptionType}; language={$this->language})",
        ])->throw();
    }
}
