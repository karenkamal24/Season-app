<?php

namespace App\Services;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Exception;

class ForgotPasswordService
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * Send OTP to user's email
     */
    public function sendOtp(string $email): void
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception('Email not found.');
        }

        $this->otpService->sendOtp($user, 'password_reset');
    }

    /**
     * Verify OTP for password reset
     */
    public function verifyOtp(string $email, string $otp): bool
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception('Email not found.');
        }

        $this->otpService->verify($user, $otp);

        return true;
    }

    /**
     * Reset password after verifying OTP
     */
    public function resetPassword(string $email, string $newPassword): void
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception('Email not found.');
        }

        if (!$user->last_otp || !$user->last_otp_expire) {
            throw new Exception('OTP not verified yet.');
        }

        $user->update([
            'password' => Hash::make($newPassword),
            'last_otp' => null,
            'last_otp_expire' => null,
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(string $email): void
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception('Email not found.');
        }

        $this->otpService->sendOtp($user, 'password_reset');
    }
}
