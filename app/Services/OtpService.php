<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\SendOtpEmailJob;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Helpers\LangHelper;
use Exception;

class OtpService
{
    protected int $otpTtl;

    public function __construct()
    {
        $this->otpTtl = (int) config('otp.ttl', 10);
    }

    public function sendOtp(User $user, string $purpose = 'verification'): void
    {
        $otpLength = (int) config('otp.length', 4);
        $min = (int) str_pad('1', $otpLength, '0');
        $max = (int) str_repeat('9', $otpLength);
        $otp = random_int($min, $max);
        $expiresAt = now()->addMinutes($this->otpTtl);

        $user->update([
            'last_otp' => Hash::make($otp),
            'last_otp_expire' => $expiresAt,
        ]);

        $subject = match ($purpose) {
            'password_reset' => LangHelper::msg('forgot_otp_sent'),
            default => LangHelper::msg('otp_sent'),
        };

        $body = LangHelper::msg('otp_sent') . "<br><b>{$otp}</b> — expires in {$this->otpTtl} minutes.";

        SendOtpEmailJob::dispatch($user->email, $subject, $body)
            ->onQueue('emails');
    }

    public function verify(User $user, string $otp): bool
    {
        if (!$user->last_otp || !$user->last_otp_expire) {
            throw new Exception(LangHelper::msg('otp_not_generated'));
        }

        $expiresAt = Carbon::parse($user->last_otp_expire);
        $now = Carbon::now();
        $isExpired = $now->gt($expiresAt);

        if ($isExpired) {
            throw new Exception(LangHelper::msg('otp_expired'));
        }

        $isValid = Hash::check($otp, $user->last_otp);

        if (!$isValid) {
            throw new Exception(LangHelper::msg('otp_invalid'));
        }

        return true;
    }
}
