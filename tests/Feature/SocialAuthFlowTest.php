<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AppleAuthService;
use App\Services\GoogleAuthService;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SocialAuthFlowTest extends TestCase
{
    private array $identities = [];

    private array $bound = [];

    protected function setUp(): void
    {
        parent::setUp();
        // Exercise the real users/token schema in an isolated SQLite memory database.
        foreach ([
            '0001_01_01_000000_create_users_table.php',
            '2025_10_16_103521_create_personal_access_tokens_table.php',
            '2025_10_27_170719_add_fcm_token_to_users_table.php',
            '2026_10_28_000000_index_users_social_provider.php',
        ] as $file) {
            (require database_path('migrations/'.$file))->up();
        }
    }

    private function identity(string $provider, array $overrides = []): void
    {
        $class = $provider === 'apple' ? AppleAuthService::class : GoogleAuthService::class;
        $this->identities[$provider] = array_replace([
            'id' => $provider.'-subject',
            'email' => 'person@example.com',
            'email_verified' => true,
            'name' => 'Google Person',
        ], $overrides);
        if (! isset($this->bound[$provider])) {
            $this->mock($class)->shouldReceive('verifyIdToken')->andReturnUsing(fn () => $this->identities[$provider]);
            $this->bound[$provider] = true;
        }
    }

    public function test_new_user_login_returns_404_then_registers_and_can_repeat_without_email(): void
    {
        foreach (['apple', 'google'] as $provider) {
            $this->identity($provider, ['email' => $provider.'@example.com']);
            $this->postJson('/api/auth/login/'.$provider, ['id_token' => 'valid'])
                ->assertNotFound()->assertJsonPath('error', 'User not registered');
            $response = $this->postJson('/api/auth/register/'.$provider, [
                'id_token' => 'valid', 'name' => 'Ahmed Ali', 'fcm_token' => 'push-token',
            ])->assertCreated()->assertJsonStructure(['data' => ['token', 'user' => ['id', 'first_name', 'last_name']]]);
            $user = User::findOrFail($response->json('data.user.id'));
            $this->assertSame($provider.'-subject', $user->provider_id);
            $this->assertNotNull(\Laravel\Sanctum\PersonalAccessToken::findToken($response->json('data.token')));
            if ($provider === 'apple') {
                $response->assertJsonPath('data.user.first_name', 'Ahmed')->assertJsonPath('data.user.last_name', 'Ali');
            }
            $this->identity($provider, ['email' => null]);
            $this->postJson('/api/auth/login/'.$provider, ['id_token' => 'valid', 'fcm_token' => ''])
                ->assertOk()->assertJsonPath('data.user.id', $user->id);
            $this->assertNull($user->fresh()->fcm_token);
            $this->postJson('/api/auth/register/'.$provider, ['id_token' => 'valid'])
                ->assertOk()->assertJsonPath('data.user.id', $user->id);
        }
        $this->assertSame(2, User::count());
    }

    public function test_login_links_verified_email_and_register_rejects_existing_email(): void
    {
        foreach (['apple', 'google'] as $provider) {
            $email = $provider.'@example.com';
            $user = User::create(['name' => 'Existing Person', 'email' => $email]);
            $this->identity($provider, ['email' => $email]);
            $this->postJson('/api/auth/register/'.$provider, ['id_token' => 'valid'])
                ->assertStatus(400)->assertJsonPath('error', 'User already registered');
            $this->postJson('/api/auth/login/'.$provider, ['id_token' => 'valid'])
                ->assertOk()->assertJsonPath('data.user.id', $user->id);
            $this->assertSame($provider, $user->fresh()->provider);
        }
    }

    public function test_apple_stores_first_time_data_and_has_missing_email_fallback(): void
    {
        $this->identity('apple', ['email' => null]);
        $this->postJson('/api/auth/register/apple', [
            'id_token' => 'valid', 'name' => 'First Last', 'email' => 'first@example.com',
        ])->assertCreated()->assertJsonPath('data.user.email', 'first@example.com');
        $this->identity('apple', ['id' => 'another', 'email' => null]);
        $this->postJson('/api/auth/register/apple', ['id_token' => 'valid'])
            ->assertCreated()->assertJsonPath('data.user.email', 'apple_another@privaterelay.season')
            ->assertJsonPath('data.user.first_name', 'Apple')->assertJsonPath('data.user.last_name', 'User');
    }

    public function test_unverified_email_does_not_link_and_other_provider_is_not_overwritten(): void
    {
        $user = User::create(['name' => 'Existing', 'email' => 'person@example.com']);
        $this->identity('apple', ['email_verified' => false]);
        $this->postJson('/api/auth/login/apple', ['id_token' => 'valid'])->assertNotFound();
        $this->assertNull($user->fresh()->provider);
        $user->update(['provider' => 'google', 'provider_id' => 'another-subject']);
        $this->identity('apple');
        $this->postJson('/api/auth/login/apple', ['id_token' => 'valid'])->assertNotFound();
        $this->assertSame('google', $user->fresh()->provider);
    }

    public function test_bad_tokens_and_validation_have_expected_responses(): void
    {
        foreach (['apple', 'google'] as $provider) {
            foreach (['login', 'register'] as $action) {
                $this->postJson('/api/auth/'.$action.'/'.$provider, [])->assertUnprocessable()
                    ->assertJsonValidationErrors('id_token');
            }
            $class = $provider === 'apple' ? AppleAuthService::class : GoogleAuthService::class;
            $this->mock($class)->shouldReceive('verifyIdToken')->andThrow(new \RuntimeException('bad signature'));
            $this->postJson('/api/auth/login/'.$provider, ['id_token' => 'bad'])
                ->assertStatus(400)->assertJsonPath('message', $provider === 'google' ? 'Invalid Google ID token' : 'Apple login failed');
        }
    }

    public function test_apple_accepts_255_character_names_and_rejects_longer_names(): void
    {
        $this->identity('apple');
        $name = str_repeat('A', 255);
        $this->postJson('/api/auth/register/apple', ['id_token' => 'valid', 'name' => $name])
            ->assertCreated()->assertJsonPath('data.user.first_name', $name);
        $this->postJson('/api/auth/register/apple', ['id_token' => 'valid', 'name' => $name.'A'])
            ->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->assertSame($name, User::firstOrFail()->name);
    }

    public function test_blocked_users_cannot_receive_social_tokens(): void
    {
        $user = User::create([
            'name' => 'Blocked', 'email' => 'person@example.com', 'is_blocked' => true,
            'provider' => 'apple', 'provider_id' => 'apple-subject',
        ]);
        $this->identity('apple');
        foreach (['login', 'register'] as $action) {
            $this->postJson('/api/auth/'.$action.'/apple', ['id_token' => 'valid'])->assertForbidden();
        }
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_migration_adds_identity_index_and_can_roll_back(): void
    {
        $this->assertTrue(Schema::hasIndex('users', ['provider', 'provider_id']));
        $migration = require database_path('migrations/2026_10_28_000000_index_users_social_provider.php');
        $migration->down();
        $this->assertFalse(Schema::hasIndex('users', ['provider', 'provider_id']));
        $this->assertTrue(Schema::hasColumn('users', 'provider_id'));
    }
}
