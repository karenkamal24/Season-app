<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Helpers\LangHelper;
use Exception;

class ForgotPasswordService
{
    public function __construct(protected OtpService $otpService) {}

    public function sendOtp(string $email): void
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception(LangHelper::msg('email_not_found'));
        }

        $this->otpService->sendOtp($user, 'password_reset');
    }

    public function verifyOtp(string $email, string $otp): bool
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception(LangHelper::msg('email_not_found'));
        }

        $this->otpService->verify($user, $otp);

        return true;
    }

    public function resetPassword(string $email, string $newPassword): void
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception(LangHelper::msg('email_not_found'));
        }

        if (!$user->last_otp || !$user->last_otp_expire) {
            throw new Exception(LangHelper::msg('otp_not_verified_yet'));
        }

        $user->update([
            'password' => Hash::make($newPassword),
            'last_otp' => null,
            'last_otp_expire' => null,
        ]);
    }

    public function resendOtp(string $email): void
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception(LangHelper::msg('email_not_found'));
        }

        $this->otpService->sendOtp($user, 'password_reset');
    }
}
