<?php

namespace App\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class AppleAuthService
{
    public function verifyIdToken(string $idToken): array
    {
        try {
            $parts = explode('.', $idToken);
            if (count($parts) !== 3) {
                throw new RuntimeException('Invalid token format');
            }
            $header = JWT::jsonDecode(JWT::urlsafeB64Decode($parts[0]));
            if (($header->alg ?? null) !== 'RS256' || empty($header->kid)) {
                throw new RuntimeException('Invalid token algorithm or missing key ID');
            }

            $keys = $this->getPublicKeys();
            if (! isset($keys[$header->kid])) {
                // Refresh once for Apple's key rotation.
                Cache::forget('apple_sign_in_jwks');
                $keys = $this->getPublicKeys();
            }
            // decode verifies the signature, algorithm, expiry and not-before claims.
            $payload = (array) JWT::decode($idToken, $keys);
            $audiences = array_filter(array_merge([
                'com.softikseason.softik',
                'com.season.app.seasonApp',
                config('services.apple.client_id'),
                config('services.apple.bundle_id'),
            ], explode(',', (string) config('services.apple.client_ids', ''))));
            $audiences = array_map('trim', $audiences);

            if (($payload['iss'] ?? null) !== 'https://appleid.apple.com'
                || ! is_string($payload['aud'] ?? null)
                || ! in_array($payload['aud'], $audiences, true)
                || ! is_numeric($payload['exp'] ?? null)
                || $payload['exp'] <= time()
                || ! is_string($payload['sub'] ?? null)
                || $payload['sub'] === '') {
                throw new RuntimeException('Invalid Apple token claims');
            }

            return [
                'id' => $payload['sub'],
                'email' => $payload['email'] ?? null,
                'email_verified' => filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        } catch (Throwable $e) {
            throw new RuntimeException('Failed to verify Apple token: '.$e->getMessage(), 0, $e);
        }
    }

    private function getPublicKeys(): array
    {
        $jwks = Cache::remember('apple_sign_in_jwks', 3600, function () {
            $jwks = Http::timeout(10)->get('https://appleid.apple.com/auth/keys')->throw()->json();
            // Validate before caching; accept only RSA signing keys.
            $jwks['keys'] = array_values(array_filter($jwks['keys'] ?? [], fn ($key) => ($key['kty'] ?? null) === 'RSA'
                && ($key['alg'] ?? null) === 'RS256'
                && ($key['use'] ?? 'sig') === 'sig'
            ));
            JWK::parseKeySet($jwks);

            return $jwks;
        });

        return JWK::parseKeySet($jwks);
    }
}
