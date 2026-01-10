<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Exception;

class GoogleAuthService
{
    /**
     * Verify Google ID token and get user info
     */
    public function verifyIdToken(string $idToken): array
    {
        try {
            $client = new GoogleClient(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($idToken);
            
            if (!$payload) {
                throw new Exception('Invalid Google ID token');
            }
            
            return [
                'id' => $payload['sub'],
                'email' => $payload['email'] ?? null,
                'email_verified' => $payload['email_verified'] ?? false,
                'name' => $payload['name'] ?? null,
                'given_name' => $payload['given_name'] ?? null,
                'family_name' => $payload['family_name'] ?? null,
                'picture' => $payload['picture'] ?? null,
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to verify Google token: ' . $e->getMessage());
        }
    }
}

