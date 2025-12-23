<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\SendOtpEmailJob;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\LangHelper;
use Exception;

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
            'password_reset' => LangHelper::msg('forgot_otp_sent'),
            default => LangHelper::msg('otp_sent'),
        };

        $body = LangHelper::msg('otp_sent') . "<br><b>{$otp}</b> — expires in {$this->otpTtl} minutes.";

        // إرسال OTP في الخلفية - response فوري بدون انتظار
        SendOtpEmailJob::dispatch($user->email, $subject, $body);

        Log::info("{$purpose} OTP {$otp} queued for {$user->email}");
    }

    public function verify(User $user, string $otp): bool
    {
        if (!$user->last_otp || !$user->last_otp_expire) {
            throw new Exception(LangHelper::msg('otp_not_generated'));
        }

        if (Carbon::now()->gt(Carbon::parse($user->last_otp_expire))) {
            throw new Exception(LangHelper::msg('otp_expired'));
        }

        if (!Hash::check($otp, $user->last_otp)) {
            throw new Exception(LangHelper::msg('otp_invalid'));
        }

        return true;
    }
}
