<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\GoogleLoginRequest;
use App\Http\Requests\Auth\AppleLoginRequest;
use App\Services\AuthService;
use App\Services\GoogleAuthService;
use App\Services\AppleAuthService;
use App\Models\User;
use App\Utils\ApiResponse;
use App\Http\Resources\Auth\UserResource;
use App\Helpers\LangHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private GoogleAuthService $googleAuth,
        private AppleAuthService $appleAuth
    ) {}

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

    /**
     * Login with Google
     */
    public function loginWithGoogle(GoogleLoginRequest $request)
    {
        Log::info('🔑 === Google Login Request Received ===', [
            'timestamp' => now()->toDateTimeString(),
        ]);

        try {
            // Verify Google token
            $googleUser = $this->googleAuth->verifyIdToken($request->id_token);
            
            // Find user by provider_id or email
            $user = User::where(function ($query) use ($googleUser) {
                $query->where(function ($q) use ($googleUser) {
                    $q->where('provider', 'google')
                      ->where('provider_id', $googleUser['id']);
                })->orWhere('email', $googleUser['email']);
            })->first();
            
            // Auto-register if user doesn't exist
            if (!$user) {
                Log::info('Auto-registering user from Google login', [
                    'email' => $googleUser['email'],
                ]);
                
                $firstName = $googleUser['given_name'] ?? null;
                $lastName = $googleUser['family_name'] ?? null;
                $fullName = $googleUser['name'] ?? trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: 'User';
                
                $user = User::create([
                    'email' => $googleUser['email'],
                    'name' => $fullName,
                    'photo_url' => $googleUser['picture'],
                    'provider' => 'google',
                    'provider_id' => $googleUser['id'],
                    'provider_token' => $request->access_token,
                    'email_verified_at' => $googleUser['email_verified'] ? now() : null,
                    'fcm_token' => $request->fcm_token,
                    'password' => bcrypt(Str::random(32)), // Random password for social login users
                ]);
                
                Log::info('✅ User auto-registered from Google login', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            } else {
                // Update FCM token if provided
                if ($request->fcm_token) {
                    $user->update(['fcm_token' => $request->fcm_token]);
                }
                
                // Update provider token
                $user->update(['provider_token' => $request->access_token]);
                
                // Update provider and provider_id if they're missing
                if (!$user->provider || !$user->provider_id) {
                    $user->update([
                        'provider' => 'google',
                        'provider_id' => $googleUser['id'],
                    ]);
                }
            }
            
            // Generate JWT token
            $token = $user->createToken('API TOKEN')->plainTextToken;
            
            Log::info('✅ Google Login completed successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'message_ar' => 'تم تسجيل الدخول بنجاح',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'first_name' => explode(' ', $user->name, 2)[0] ?? null,
                        'last_name' => explode(' ', $user->name, 2)[1] ?? null,
                        'name' => $user->name,
                        'photo' => $user->photo_url ?? $user->avatar ?? null,
                        'phone' => $user->phone,
                        'email_verified_at' => $user->email_verified_at,
                    ]
                ]
            ], 200);
            
        } catch (Exception $e) {
            Log::error('❌ Google Login failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Google login failed',
                'message_ar' => 'فشل تسجيل الدخول عبر Google',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Register with Google
     */
    public function registerWithGoogle(GoogleLoginRequest $request)
    {
        Log::info('📝 === Google Register Request Received ===', [
            'timestamp' => now()->toDateTimeString(),
        ]);

        try {
            // Verify Google token
            $googleUser = $this->googleAuth->verifyIdToken($request->id_token);
            
            // Check if user already exists
            $existingUser = User::where('email', $googleUser['email'])->first();
            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already exists. Please login instead.',
                    'message_ar' => 'المستخدم موجود بالفعل. يرجى تسجيل الدخول',
                    'error' => 'User already registered'
                ], 400);
            }
            
            // Create new user
            $firstName = $googleUser['given_name'] ?? null;
            $lastName = $googleUser['family_name'] ?? null;
            $fullName = $googleUser['name'] ?? trim(($firstName ?? '') . ' ' . ($lastName ?? '')) ?: 'User';
            
            $user = User::create([
                'email' => $googleUser['email'],
                'name' => $fullName,
                'photo_url' => $googleUser['picture'],
                'provider' => 'google',
                'provider_id' => $googleUser['id'],
                'provider_token' => $request->access_token,
                'email_verified_at' => $googleUser['email_verified'] ? now() : null,
                'fcm_token' => $request->fcm_token,
                'password' => bcrypt(Str::random(32)), // Random password for social login users
            ]);
            
            // Generate JWT token
            $token = $user->createToken('API TOKEN')->plainTextToken;
            
            Log::info('✅ Google Register completed successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'message_ar' => 'تم التسجيل بنجاح',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'first_name' => explode(' ', $user->name, 2)[0] ?? null,
                        'last_name' => explode(' ', $user->name, 2)[1] ?? null,
                        'name' => $user->name,
                        'photo' => $user->photo_url ?? $user->avatar ?? null,
                        'phone' => $user->phone,
                        'email_verified_at' => $user->email_verified_at,
                    ]
                ]
            ], 201);
            
        } catch (Exception $e) {
            Log::error('❌ Google Register failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Google registration failed',
                'message_ar' => 'فشل التسجيل عبر Google',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Login with Apple
     */
    public function loginWithApple(AppleLoginRequest $request)
    {
        Log::info('🔑 === Apple Login Request Received ===', [
            'timestamp' => now()->toDateTimeString(),
        ]);

        try {
            // Verify Apple token
            $appleUser = $this->appleAuth->verifyIdToken($request->id_token);
            
            // Find user by provider_id or email
            $user = User::where(function ($query) use ($appleUser) {
                $query->where(function ($q) use ($appleUser) {
                    $q->where('provider', 'apple')
                      ->where('provider_id', $appleUser['id']);
                })->orWhere('email', $appleUser['email']);
            })->first();
            
            // Auto-register if user doesn't exist
            if (!$user) {
                Log::info('Auto-registering user from Apple login', [
                    'email' => $appleUser['email'],
                ]);
                
                $user = User::create([
                    'email' => $appleUser['email'],
                    'name' => 'User', // Apple doesn't provide name in subsequent logins
                    'provider' => 'apple',
                    'provider_id' => $appleUser['id'],
                    'provider_token' => $request->authorization_code,
                    'email_verified_at' => $appleUser['email_verified'] ? now() : null,
                    'fcm_token' => $request->fcm_token,
                    'password' => bcrypt(Str::random(32)), // Random password for social login users
                ]);
                
                Log::info('✅ User auto-registered from Apple login', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            } else {
                // Update FCM token if provided
                if ($request->fcm_token) {
                    $user->update(['fcm_token' => $request->fcm_token]);
                }
                
                // Update provider token if authorization_code provided
                if ($request->authorization_code) {
                    $user->update(['provider_token' => $request->authorization_code]);
                }
                
                // Update provider and provider_id if they're missing
                if (!$user->provider || !$user->provider_id) {
                    $user->update([
                        'provider' => 'apple',
                        'provider_id' => $appleUser['id'],
                    ]);
                }
            }
            
            // Generate JWT token
            $token = $user->createToken('API TOKEN')->plainTextToken;
            
            Log::info('✅ Apple Login completed successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'message_ar' => 'تم تسجيل الدخول بنجاح',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'first_name' => explode(' ', $user->name, 2)[0] ?? null,
                        'last_name' => explode(' ', $user->name, 2)[1] ?? null,
                        'name' => $user->name,
                        'photo' => null, // Apple doesn't provide photo
                        'phone' => $user->phone,
                        'email_verified_at' => $user->email_verified_at,
                    ]
                ]
            ], 200);
            
        } catch (Exception $e) {
            Log::error('❌ Apple Login failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Apple login failed',
                'message_ar' => 'فشل تسجيل الدخول عبر Apple',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Register with Apple
     */
    public function registerWithApple(AppleLoginRequest $request)
    {
        Log::info('📝 === Apple Register Request Received ===', [
            'timestamp' => now()->toDateTimeString(),
        ]);

        try {
            // Verify Apple token
            $appleUser = $this->appleAuth->verifyIdToken($request->id_token);
            
            // Check if user already exists
            $existingUser = User::where('email', $appleUser['email'])->first();
            if ($existingUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already exists. Please login instead.',
                    'message_ar' => 'المستخدم موجود بالفعل. يرجى تسجيل الدخول',
                    'error' => 'User already registered'
                ], 400);
            }
            
            // Note: Apple only provides name on first sign-in
            // Create new user
            $user = User::create([
                'email' => $appleUser['email'],
                'name' => 'User', // Apple doesn't provide name in subsequent logins
                'provider' => 'apple',
                'provider_id' => $appleUser['id'],
                'provider_token' => $request->authorization_code,
                'email_verified_at' => $appleUser['email_verified'] ? now() : null,
                'fcm_token' => $request->fcm_token,
                'password' => bcrypt(Str::random(32)), // Random password for social login users
            ]);
            
            // Generate JWT token
            $token = $user->createToken('API TOKEN')->plainTextToken;
            
            Log::info('✅ Apple Register completed successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'message_ar' => 'تم التسجيل بنجاح',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'first_name' => explode(' ', $user->name, 2)[0] ?? null,
                        'last_name' => explode(' ', $user->name, 2)[1] ?? null,
                        'name' => $user->name,
                        'photo' => null, // Apple doesn't provide photo
                        'phone' => $user->phone,
                        'email_verified_at' => $user->email_verified_at,
                    ]
                ]
            ], 201);
            
        } catch (Exception $e) {
            Log::error('❌ Apple Register failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Apple registration failed',
                'message_ar' => 'فشل التسجيل عبر Apple',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
