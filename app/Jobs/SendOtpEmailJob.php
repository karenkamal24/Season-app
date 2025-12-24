<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Mail\SendGridMail;
use App\Models\User;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;  // ⬅️ غيّرنا من $user لـ $userId
    public $userEmail;  // ⬅️ أضفنا email مباشرةً
    public $otp;
    public $purpose;
    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $otp, $purpose)
    {
        // ✅ نخزّن الـ ID والـ Email بدل الـ Object كله
        $this->userId = is_object($user) ? $user->id : $user;
        $this->userEmail = is_object($user) ? $user->email : null;
        $this->otp = $otp;
        $this->purpose = $purpose;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('📧 بدء Job: SendOtpEmailJob', [
            'user_id' => $this->userId,
            'user_email' => $this->userEmail,
            'subject' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني بنجاح.',
            'queue' => 'emails',
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
            'timestamp' => now()
        ]);

        try {
            // ✅ لو الـ email مش موجود، نجيبه من الـ Database
            $email = $this->userEmail;
            
            if (!$email && $this->userId) {
                $user = User::find($this->userId);
                $email = $user ? $user->email : null;
            }
            
            if (!$email) {
                throw new \Exception('User email not found');
            }

            $subject = 'تم إرسال رمز التحقق إلى بريدك الإلكتروني بنجاح.';
            $body = "تم إرسال رمز التحقق إلى بريدك الإلكتروني بنجاح.<br><b>{$this->otp}</b> — expires in 10 minutes.";
            
            // ✅ استخدم SendGrid API
            $result = SendGridMail::send($email, $subject, $body);

            Log::info('✅ SendOtpEmailJob نجح', [
                'user_id' => $this->userId,
                'user_email' => $email,
                'result' => $result,
                'timestamp' => now()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ SendOtpEmailJob فشل', [
                'user_id' => $this->userId,
                'user_email' => $this->userEmail,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
                'timestamp' => now()
            ]);

            // ✅ أعد المحاولة لو لسه في محاولات
            if ($this->attempts() < $this->tries) {
                $this->release(10); // retry بعد 10 ثواني
            } else {
                throw $e; // fail نهائياً
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('❌ SendOtpEmailJob فشل نهائياً', [
            'user_id' => $this->userId,
            'user_email' => $this->userEmail,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}