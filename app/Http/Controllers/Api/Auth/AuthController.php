<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Utils\ApiResponse;
use App\Http\Resources\Auth\UserResource;
use App\Helpers\LangHelper;
use Illuminate\Support\Facades\Log; 
use Exception;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    /**
     * Register — send OTP to email
     */
    public function register(RegisterRequest $request)
    {
        try {
            $this->authService->register($request->validated());
            return ApiResponse::created(LangHelper::msg('otp_sent'));
        } catch (Exception $e) {
            return ApiResponse::badRequest($e->getMessage());
        }
    }

    /**
     * Verify OTP — complete registration
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        Log::info('🔐 === Verify OTP Request Received ===', [
            'email' => $request->email ?? 'not provided',
            'otp_provided' => $request->otp ?? 'not provided',
            'timestamp' => now()->toDateTimeString(),
        ]);

        try {
            $result = $this->authService->verifyOtp(
                $request->email,
                $request->otp
            );

            Log::info('✅ Verify OTP completed successfully', [
                'email' => $request->email ?? 'not provided',
                'user_id' => $result['user']->id ?? 'N/A',
            ]);

            return response()->json([
                'message'  => LangHelper::msg('login_success'),
                'token'    => $result['token'],
                'url'      => null,
                'userInfo' => new UserResource($result['user']),
            ], 200);
        } catch (Exception $e) {
            Log::error('❌ Verify OTP failed', [
                'email' => $request->email ?? 'not provided',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return ApiResponse::badRequest($e->getMessage());
        }
    }

    /**
     * Resend OTP — send again if not verified
     */
    public function resendOtp(ResendOtpRequest $request)
    {
        Log::info('🔄 === Resend OTP Request Received ===', [
            'email' => $request->email,
            'timestamp' => now()->toDateTimeString(),
        ]);

        try {
            $this->authService->resendOtp($request->email);

            Log::info('✅ Resend OTP completed successfully', [
                'email' => $request->email,
            ]);

            return ApiResponse::success(LangHelper::msg('otp_resent'), []);
        } catch (Exception $e) {
            Log::error('❌ Resend OTP failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return ApiResponse::badRequest($e->getMessage());
        }
    }

    /**
     * Login
     */
    public function login(LoginRequest $request)
    {
        Log::info('🔑 === Login Request Received ===', [
            'email' => $request->email ?? 'not provided',
            'timestamp' => now()->toDateTimeString(),
        ]);

        try {
            $result = $this->authService->login($request->validated());

            Log::info('✅ Login completed successfully', [
                'email' => $request->email ?? 'not provided',
                'user_id' => $result['user']->id ?? 'N/A',
            ]);

            return response()->json([
                'message'  => LangHelper::msg('login_success'),
                'token'    => $result['token'],
                'url'      => null,
                'userInfo' => new UserResource($result['user']),
            ], 200);
        } catch (Exception $e) {
            Log::error('❌ Login failed', [
                'email' => $request->email ?? 'not provided',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return ApiResponse::badRequest($e->getMessage());
        }
    }
}
