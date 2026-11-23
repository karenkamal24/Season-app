<?php

namespace Tests\Feature;

use App\Services\AppleAuthService;
use App\Services\GoogleAuthService;
use Firebase\JWT\JWT;
use Google\Client as GoogleClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SocialTokenVerificationTest extends TestCase
{
    private string $privateKey = '';

    private array $jwks;

    private int $appleStatus = 200;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        JWT::$leeway = 0;
        $options = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
        $windowsConfig = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf';
        if (is_file($windowsConfig)) {
            $options['config'] = $windowsConfig;
        }
        $key = openssl_pkey_new($options);
        openssl_pkey_export($key, $this->privateKey, null, $options);
        $rsa = openssl_pkey_get_details($key)['rsa'];
        $this->jwks = ['keys' => [[
            'kid' => 'test-key', 'kty' => 'RSA', 'alg' => 'RS256', 'use' => 'sig',
            'n' => JWT::urlsafeB64Encode($rsa['n']), 'e' => JWT::urlsafeB64Encode($rsa['e']),
        ]]];
        Http::preventStrayRequests();
        Http::fake(['appleid.apple.com/auth/keys' => fn () => Http::response($this->jwks, $this->appleStatus)]);
    }

    private function token(array $overrides = []): string
    {
        return JWT::encode(array_replace([
            'iss' => 'https://appleid.apple.com', 'aud' => 'com.softikseason.softik',
            'sub' => 'apple-subject', 'exp' => time() + 600, 'iat' => time(),
        ], $overrides), $this->privateKey, 'RS256', 'test-key');
    }

    public function test_apple_accepts_new_and_legacy_audiences_and_caches_keys(): void
    {
        $service = new AppleAuthService;
        foreach (['com.softikseason.softik', 'com.season.app.seasonApp'] as $aud) {
            $this->assertSame('apple-subject', $service->verifyIdToken($this->token(['aud' => $aud]))['id']);
        }
        Http::assertSentCount(1);
    }

    public function test_apple_rejects_invalid_claims_and_signature(): void
    {
        $invalid = [
            $this->token(['aud' => 'com.season.app.attacker']),
            $this->token(['iss' => 'https://attacker.example']),
            $this->token(['exp' => time() - 60]),
            $this->token(['exp' => null]),
            $this->token(['sub' => '']),
            $this->token(['sub' => null]),
            $this->token(['nbf' => time() + 600]),
            JWT::encode(['sub' => 'forged'], 'attacker-key', 'HS256', 'test-key'),
        ];
        $valid = explode('.', $this->token());
        $valid[1] = JWT::urlsafeB64Encode(json_encode(['sub' => 'forged']));
        $invalid[] = implode('.', $valid);
        foreach ($invalid as $token) {
            try {
                (new AppleAuthService)->verifyIdToken($token);
                $this->fail('Accepted invalid Apple token');
            } catch (\RuntimeException $e) {
                $this->assertStringContainsString('Failed to verify Apple token', $e->getMessage());
            }
        }
    }

    public function test_apple_fails_closed_when_keys_cannot_be_fetched(): void
    {
        $this->appleStatus = 503;
        $this->expectException(\RuntimeException::class);
        (new AppleAuthService)->verifyIdToken($this->token());
    }

    public function test_apple_refreshes_keys_for_an_unknown_kid(): void
    {
        Cache::put('apple_sign_in_jwks', ['keys' => [array_replace($this->jwks['keys'][0], ['kid' => 'old'])]], 3600);
        $this->assertSame('apple-subject', (new AppleAuthService)->verifyIdToken($this->token())['id']);
        Http::assertSentCount(1);
    }

    private function googleService(): GoogleAuthService
    {
        $client = new GoogleClient;
        $client->setHttpClient(new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode($this->jwks)),
        ]))]));

        return new GoogleAuthService($client);
    }

    public function test_google_accepts_both_web_clients_with_real_signature_verification(): void
    {
        foreach ([
            '947193806162-a2brd7oi08orov298knjtn2ntc7o975d.apps.googleusercontent.com',
            '947193806162-nua0frbdtn89jipohvfn43bbbc8r86er.apps.googleusercontent.com',
        ] as $aud) {
            $token = $this->token(['aud' => $aud, 'iss' => 'https://accounts.google.com']);
            $this->assertSame('apple-subject', $this->googleService()->verifyIdToken($token)['id']);
        }
    }

    public function test_google_rejects_wrong_audience_expiry_issuer_and_forgery(): void
    {
        $aud = '947193806162-a2brd7oi08orov298knjtn2ntc7o975d.apps.googleusercontent.com';
        foreach ([
            ['aud' => 'attacker.apps.googleusercontent.com'],
            ['iss' => 'https://attacker.example'],
            ['exp' => time() - 60],
            ['exp' => null],
            ['sub' => ''],
        ] as $change) {
            $token = $this->token(array_replace(['aud' => $aud, 'iss' => 'https://accounts.google.com'], $change));
            try {
                $this->googleService()->verifyIdToken($token);
                $this->fail('Accepted invalid Google token');
            } catch (\RuntimeException $e) {
                $this->assertStringContainsString('Invalid Google ID token', $e->getMessage());
            }
        }
        $parts = explode('.', $this->token(['aud' => $aud, 'iss' => 'https://accounts.google.com']));
        $parts[1] = JWT::urlsafeB64Encode(json_encode(['sub' => 'forged']));
        $this->expectExceptionMessage('Invalid Google ID token');
        $this->googleService()->verifyIdToken(implode('.', $parts));
    }
}
