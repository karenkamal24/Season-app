<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\Auth\VendorServiceController;
use App\Http\Controllers\Api\Location\CountryController;
use App\Http\Controllers\Api\Location\CityController;
use App\Http\Controllers\Api\Emergency\EmergencyController;
use App\Http\Controllers\Api\QR\UserQrController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication
Route::prefix('auth')
    ->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
        Route::post('/login', [AuthController::class, 'login']);
    });
//foeget passwoed and  Password Reset
Route::prefix('auth')
    ->group(function () {
        Route::post('forgot-password', [ForgotPasswordController::class, 'sendOtp']);
        Route::post('verify-reset-otp', [ForgotPasswordController::class, 'verifyOtp']);
        Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword']);
        Route::post('resend-reset-otp', [ForgotPasswordController::class, 'resendOtp']);
    });
// Profile
Route::middleware('auth:sanctum')
    ->prefix('profile')
    ->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
    });
//vendor
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('vendor-services')->group(function () {
        Route::get('/', [VendorServiceController::class, 'index']);
        Route::get('/{id}', [VendorServiceController::class, 'show']);
        Route::post('/', [VendorServiceController::class, 'store']);
        Route::put('/{id}', [VendorServiceController::class, 'update']);
        Route::delete('/{id}', [VendorServiceController::class, 'destroy']);
    });
});
//service types
Route::get('service-types', [VendorServiceController::class, 'indexServiceType']);
//emergency
Route::get('/emergency', [EmergencyController::class, 'show']);


Route::prefix('Location')->group(function () {
    Route::get('/countries', [CountryController::class, 'index']);
    Route::get('/countries/{id}', [CountryController::class, 'show']);
    Route::get('/cities', [CityController::class, 'index']);
    Route::get('/cities/{id}', [CityController::class, 'show']);
});


Route::get('/user/qr', [UserQrController::class, 'generate'])->middleware('auth:sanctum');

