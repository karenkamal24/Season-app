<?php

namespace App\Services;

use Google\Client as GoogleClient;
use RuntimeException;
use Throwable;

class GoogleAuthService
{
    public function __construct(private ?GoogleClient $client = null) {}

    public function verifyIdToken(string $idToken): array
    {
        try {
            // Google verifies signature/issuer/time; validate multiple Web audiences below.
            $client = $this->client ?? new GoogleClient;
            $client->setClientId('');
            $payload = $client->verifyIdToken($idToken);
            $allowed = array_filter(array_merge([
                '947193806162-a2brd7oi08orov298knjtn2ntc7o975d.apps.googleusercontent.com',
                '947193806162-nua0frbdtn89jipohvfn43bbbc8r86er.apps.googleusercontent.com',
                config('services.google.client_id'),
            ], explode(',', (string) config('services.google.client_ids', ''))));

            if (! $payload
                || ! is_string($payload['aud'] ?? null)
                || ! in_array($payload['aud'], array_map('trim', $allowed), true)
                || ! in_array($payload['iss'] ?? null, ['accounts.google.com', 'https://accounts.google.com'], true)
                || ! is_numeric($payload['exp'] ?? null)
                || $payload['exp'] <= time()
                || ! is_string($payload['sub'] ?? null)
                || $payload['sub'] === '') {
                throw new RuntimeException('Invalid Google ID token');
            }

            return [
                'id' => $payload['sub'],
                'email' => $payload['email'] ?? null,
                'email_verified' => filter_var($payload['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'name' => $payload['name'] ?? null,
                'given_name' => $payload['given_name'] ?? null,
                'family_name' => $payload['family_name'] ?? null,
                'picture' => $payload['picture'] ?? null,
            ];
        } catch (Throwable $e) {
            throw new RuntimeException('Invalid Google ID token: '.$e->getMessage(), 0, $e);
        }
    }
}
