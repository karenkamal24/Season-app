<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helpers\LangHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AppleLoginRequest;
use App\Http\Requests\Auth\GoogleLoginRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\Auth\UserResource;
use App\Models\User;
use App\Services\AppleAuthService;
use App\Services\AuthService;
use App\Services\GoogleAuthService;
use App\Utils\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                'message' => LangHelper::msg('login_success'),
                'token' => $result['token'],
                'url' => null,
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
                'message' => LangHelper::msg('login_success'),
                'token' => $result['token'],
                'url' => null,
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

    public function loginWithGoogle(GoogleLoginRequest $request)
    {
        return $this->socialAuth($request, 'google', false);
    }

    public function registerWithGoogle(GoogleLoginRequest $request)
    {
        return $this->socialAuth($request, 'google', true);
    }

    public function loginWithApple(AppleLoginRequest $request)
    {
        return $this->socialAuth($request, 'apple', false);
    }

    public function registerWithApple(AppleLoginRequest $request)
    {
        return $this->socialAuth($request, 'apple', true);
    }

    private function socialAuth(\Illuminate\Http\Request $request, string $provider, bool $register)
    {
        try {
            $identity = $provider === 'apple'
                ? $this->appleAuth->verifyIdToken($request->input('id_token'))
                : $this->googleAuth->verifyIdToken($request->input('id_token'));
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $provider === 'google' ? 'Invalid Google ID token' : 'Apple login failed',
                'error' => $e->getMessage(),
            ], 400);
        }

        $user = User::where('provider', $provider)->where('provider_id', $identity['id'])->first();
        $created = false;
        $tokenEmail = $identity['email'] ?? null;

        if (! $user && ! $register && $tokenEmail) {
            // Only a provider-verified email can link an existing account.
            if ($identity['email_verified'] ?? false) {
                $user = User::where('email', $tokenEmail)
                    ->whereNull('provider')->whereNull('provider_id')->first();
                if ($user) {
                    $user->fill(['provider' => $provider, 'provider_id' => $identity['id']]);
                }
            }
        }

        if (! $user && ! $register) {
            return response()->json([
                'success' => false,
                'message' => 'User not found. Please register first.',
                'message_ar' => 'المستخدم غير موجود. يرجى التسجيل أولاً',
                'error' => 'User not registered',
            ], 404);
        }

        if (! $user) {
            $email = $tokenEmail ?: ($provider === 'apple' ? $request->input('email') : null);
            $email = $email ?: $provider.'_'.$identity['id'].'@privaterelay.season';
            if (User::where('email', $email)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already exists. Please login instead.',
                    'error' => 'User already registered',
                ], 400);
            }

            $name = $provider === 'apple'
                ? $request->input('name')
                : ($identity['name'] ?? trim(($identity['given_name'] ?? '').' '.($identity['family_name'] ?? '')));
            $name = trim((string) $name) ?: ucfirst($provider).' User';
            $user = new User([
                'email' => $email,
                // The existing schema stores one name; response splits it into first/last.
                'name' => $name,
                'photo_url' => $identity['picture'] ?? null,
                'provider' => $provider,
                'provider_id' => $identity['id'],
                'email_verified_at' => $tokenEmail && ($identity['email_verified'] ?? false) ? now() : null,
                'password' => bcrypt(Str::random(32)),
            ]);
            $created = true;
        }

        if ($user->is_blocked) {
            return response()->json(['success' => false, 'message' => 'Account is blocked'], 403);
        }
        if ($request->exists('fcm_token')) {
            $user->fcm_token = $request->input('fcm_token');
        }
        $user->save();
        // Keep the same Sanctum bearer token used by ordinary login.
        $token = $user->createToken('API TOKEN')->plainTextToken;
        $names = explode(' ', $user->name, 2);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'message_ar' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'first_name' => $names[0],
                    'last_name' => $names[1] ?? null,
                    'name' => $user->name,
                    'photo' => $user->photo_url ?? $user->avatar,
                    'phone' => $user->phone,
                    'email_verified_at' => $user->email_verified_at,
                ],
            ],
        ], $created ? 201 : 200);
    }
}
