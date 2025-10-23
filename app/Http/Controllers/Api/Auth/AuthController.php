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
        try {
            $result = $this->authService->verifyOtp(
                $request->email,
                $request->otp
            );

            return response()->json([
                'message'  => LangHelper::msg('login_success'),
                'token'    => $result['token'],
                'url'      => null,
                'userInfo' => new UserResource($result['user']),
            ], 200);
        } catch (Exception $e) {
            return ApiResponse::badRequest($e->getMessage());
        }
    }

    /**
     * Resend OTP — send again if not verified
     */
    public function resendOtp(ResendOtpRequest $request)
    {
        try {
            $this->authService->resendOtp($request->email);
            return ApiResponse::success(LangHelper::msg('otp_resent'), []);
        } catch (Exception $e) {
            return ApiResponse::badRequest($e->getMessage());
        }
    }

    /**
     * Login
     */
    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login($request->validated());

            return response()->json([
                'message'  => LangHelper::msg('login_success'),
                'token'    => $result['token'],
                'url'      => null,
                'userInfo' => new UserResource($result['user']),
            ], 200);
        } catch (Exception $e) {
            return ApiResponse::badRequest($e->getMessage());
        }
    }
}
