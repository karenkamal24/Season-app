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
            $clientId = config('services.google.client_id');
            
            if (!$clientId) {
                throw new Exception('Google client_id is not configured. Please set GOOGLE_CLIENT_ID in your .env file.');
            }
            
            $client = new GoogleClient(['client_id' => $clientId]);
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
        } catch (\Google\Exception $e) {
            throw new Exception('Google authentication error: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to verify Google token: ' . $e->getMessage());
        }
    }
}

