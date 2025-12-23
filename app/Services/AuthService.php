<?php

namespace App\Services;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthService
{
    public function __construct(protected OtpService $otpService) {}

    /**
     * Simple translation helper (without lang files)
     */
    private function msg(string $key): string
    {
        $locale = app()->getLocale();

        $messages = [
            'email_registered'      => ['en' => 'This email is already registered.', 'ar' => 'هذا البريد الإلكتروني مسجل بالفعل.'],
            'email_not_found'       => ['en' => 'Email not found.', 'ar' => 'البريد الإلكتروني غير موجود.'],
            'email_verified'        => ['en' => 'This email is already verified.', 'ar' => 'تم تفعيل هذا البريد الإلكتروني من قبل.'],
            'invalid_credentials'   => ['en' => 'Invalid email or password.', 'ar' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.'],
            'not_verified'          => ['en' => 'Your email is not verified. Please verify your account first.', 'ar' => 'لم يتم تفعيل بريدك الإلكتروني بعد. يرجى تفعيله أولاً.'],
        ];

        return $messages[$key][$locale] ?? $messages[$key]['en'] ?? $key;
    }

    /**
     * Register new user and send OTP
     */
    public function register(array $data): void
    {
        Log::info('🔐 AuthService::register called', [
            'email' => $data['email'] ?? 'not provided',
            'timestamp' => now()->toDateTimeString(),
        ]);

        $email = strtolower(trim($data['email']));
        $existing = User::where('email', $email)->first();

        if ($existing && $existing->email_verified_at) {
            Log::warning('Email already registered', ['email' => $email]);
            throw new Exception($this->msg('email_registered'));
        }

        if ($existing && !$existing->email_verified_at) {
            Log::info('Deleting unverified existing user', ['email' => $email]);
            $existing->delete();
        }

        Log::info('Creating new user', ['email' => $email]);

        $user = User::create([
            'name'     => trim($data['first_name'] . ' ' . $data['last_name']),
            'email'    => $email,
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        Log::info('User created, calling sendOtp', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $this->otpService->sendOtp($user, 'verification');

        Log::info('sendOtp called successfully', ['user_id' => $user->id]);
    }

    /**
     * Verify OTP and activate user
     */
    public function verifyOtp(string $email, string $otp): array
    {
        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            throw new Exception($this->msg('email_not_found'));
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
        Log::info('🔄 AuthService::resendOtp called', [
            'email' => $email,
            'timestamp' => now()->toDateTimeString(),
        ]);

        $user = User::where('email', strtolower(trim($email)))->first();

        if (!$user) {
            Log::warning('User not found for resend OTP', ['email' => $email]);
            throw new Exception($this->msg('email_not_found'));
        }

        if ($user->email_verified_at) {
            Log::warning('Email already verified', ['email' => $email]);
            throw new Exception($this->msg('email_verified'));
        }

        Log::info('Calling sendOtp for resend', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $this->otpService->sendOtp($user, 'verification');

        Log::info('sendOtp called successfully for resend', ['user_id' => $user->id]);
    }

    /**
     * Login user
     */
    public function login(array $credentials): array
    {
        $email = strtolower(trim($credentials['email']));
        $user = User::where('email', $email)->first();

        if (!$user || !\Hash::check($credentials['password'], $user->password)) {
            throw new Exception($this->msg('invalid_credentials'));
        }

        if (!$user->email_verified_at) {
            throw new Exception($this->msg('not_verified'));
        }

        if (!empty($credentials['fcm_token'])) {
            $user->update(['fcm_token' => $credentials['fcm_token']]);
        }

        $token = $user->createToken('API TOKEN')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
