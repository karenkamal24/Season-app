<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\VerifyResetOtpRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Services\ForgotPasswordService;
use App\Utils\ApiResponse;
use Exception;

class ForgotPasswordController extends Controller
{
    public function __construct(private ForgotPasswordService $forgotPasswordService) {}

    public function sendOtp(ForgotPasswordRequest $request)
    {
        try {
            $this->forgotPasswordService->sendOtp($request->email);
            return ApiResponse::success('OTP sent to your email.');
        } catch (Exception $e) {
            return ApiResponse::badRequest($e->getMessage());
        }
    }

    public function verifyOtp(VerifyResetOtpRequest $request)
    {
        try {
            $this->forgotPasswordService->verifyOtp($request->email, $request->otp);
            return ApiResponse::success('OTP verified successfully.');
        } catch (Exception $e) {
            return ApiResponse::badRequest($e->getMessage());
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->forgotPasswordService->resetPassword($request->email, $request->password);
            return ApiResponse::success('Password reset successfully.');
        } catch (Exception $e) {
            return ApiResponse::badRequest($e->getMessage());
        }
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        try {
            $this->forgotPasswordService->resendOtp($request->email);
            return ApiResponse::success('A new OTP has been sent to your email.');
        } catch (Exception $e) {
            return ApiResponse::badRequest($e->getMessage());
        }
    }
    
}
