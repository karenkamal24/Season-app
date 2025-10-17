<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\SendOtpEmailJob;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OtpService
{
    protected int $otpTtl = 10; 


    public function sendOtp(User $user, string $purpose = 'verification'): void
    {
        $otp = random_int(1000, 9999);
        $expiresAt = now()->addMinutes($this->otpTtl);

        $user->update([
            'last_otp' => Hash::make($otp),
            'last_otp_expire' => $expiresAt,
        ]);

        $subject = match ($purpose) {
            'password_reset' => 'Your password reset code',
            default => 'Your verification code',
        };

        $body = "Your {$purpose} OTP is <b>{$otp}</b>. It will expire in {$this->otpTtl} minutes.";

        SendOtpEmailJob::dispatch($user->email, $subject, $body);

        Log::info("{$purpose} OTP {$otp} sent to {$user->email}");
    }


    public function verify(User $user, string $otp): bool
    {
        if (!$user->last_otp || !$user->last_otp_expire) {
            throw new \Exception('OTP not generated.');
        }

        if (Carbon::now()->gt(Carbon::parse($user->last_otp_expire))) {
            throw new \Exception('OTP expired.');
        }

        if (!Hash::check($otp, $user->last_otp)) {
            throw new \Exception('Invalid OTP.');
        }

        return true;
    }
}
