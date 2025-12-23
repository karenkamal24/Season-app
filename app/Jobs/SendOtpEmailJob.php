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
        $this->sendEmail($this->email, $this->subject, $this->body);
    }
}
