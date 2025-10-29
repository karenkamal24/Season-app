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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Api\Group\GroupController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

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
        Route::post('/language', [ProfileController::class, 'updateLanguage']);
    });
//vendor
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('vendor-services')->group(function () {
        Route::get('/', [VendorServiceController::class, 'index']);
        Route::get('/{id}', [VendorServiceController::class, 'show']);
        Route::post('/', [VendorServiceController::class, 'store']);
        Route::put('/{id}', [VendorServiceController::class, 'update']);
        Route::delete('/{id}', [VendorServiceController::class, 'destroy']);
        Route::post('/{id}/enable', [VendorServiceController::class, 'enable']);
        Route::delete('/{id}/forceDelete', [VendorServiceController::class, 'forceDelete']);
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


Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::post('/send-to-user', [NotificationController::class, 'sendToUser']);
    Route::post('/send-to-all', [NotificationController::class, 'sendToAll']);
    Route::post('/send-to-multiple', [NotificationController::class, 'sendToMultiple']);
});

Route::middleware('auth:sanctum')->prefix('groups')->group(function () {
    Route::get('/', [GroupController::class, 'index']);
    Route::post('/', [GroupController::class, 'store']);
    Route::get('/{id}', [GroupController::class, 'show']);
    Route::put('/{id}', [GroupController::class, 'update']);
    Route::delete('/{id}', [GroupController::class, 'destroy']);
    Route::post('/join', [GroupController::class, 'join']);
    Route::post('/{id}/leave', [GroupController::class, 'leave']);
    Route::get('/{id}/members', [GroupController::class, 'members']);
    Route::delete('/{groupId}/members/{userId}', [GroupController::class, 'removeMember']);
    Route::post('/{id}/location', [GroupController::class, 'updateLocation']);
    Route::post('/{id}/sos', [GroupController::class, 'sendSos']);
    Route::post('/{groupId}/sos/{alertId}/resolve', [GroupController::class, 'resolveSos']);
});
Route::get('/groups/invite/{inviteCode}', [GroupController::class, 'inviteDetails']);

