<?php

namespace App\Services;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;

class AuthService
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * Register new user and send OTP
     */
    public function register(array $data): void
    {
        $email = strtolower(trim($data['email']));

        $existing = User::where('email', $email)->first();

        if ($existing && $existing->email_verified_at) {
            throw new Exception('This email is already registered.');
        }

        if ($existing && !$existing->email_verified_at) {
            $existing->delete();
        }

        $user = User::create([
            'name'     => trim($data['first_name'] . ' ' . $data['last_name']),
            'email'    => $email,
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        $this->otpService->sendOtp($user, 'verification');

      
    }

    /**
     * Verify OTP and activate user
     */
    public function verifyOtp(string $email, string $otp): array
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception('Email not found.');
        }

        $this->otpService->verify($user, $otp);

        $user->update([
            'email_verified_at' => now(),
            'last_otp' => null,
            'last_otp_expire' => null,
        ]);

        $token = $user->createToken('API TOKEN')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * Resend OTP for unverified users
     */
    public function resendOtp(string $email): void
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception('Email not found.');
        }

        if ($user->email_verified_at) {
            throw new Exception('This email is already verified.');
        }

        $this->otpService->sendOtp($user, 'verification');
    }
    public function login(array $credentials): array
{
    $email = strtolower(trim($credentials['email']));
    $user = User::where('email', $email)->first();

    if (!$user || !\Hash::check($credentials['password'], $user->password)) {
        throw new \Exception('Invalid email or password.');
    }

    if (!$user->email_verified_at) {
        throw new \Exception('Your email is not verified. Please verify your account first.');
    }


    if (!empty($credentials['notification_token'])) {
        $user->update(['notification_token' => $credentials['notification_token']]);
    }

    $token = $user->createToken('API TOKEN')->plainTextToken;

    return [
        'user' => $user,
        'token' => $token,
    ];
}

}
