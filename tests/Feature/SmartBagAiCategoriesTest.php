<?php

namespace Tests\Feature;

use App\Jobs\SendAiFailureAlert;
use App\Http\Controllers\Api\BagController;
use App\Models\User;
use App\Http\Middleware\UpdateUserLastActive;
use App\Services\GeminiAIService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SmartBagAiCategoriesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config()->set('smart_bag_ai.alert_webhook_url', 'https://example.com/ai-alert');
        Queue::fake();
        $this->withoutMiddleware(UpdateUserLastActive::class);
        $this->actingAs(new User(['name' => 'Tester']));
    }

    public function test_success_caches_ai_categories_for_one_day(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => '[{"name":"Clothes"}]']]]]],
        ])]);

        $this->getJson('/api/smart-bags/ai/categories?language=en')
            ->assertOk()->assertJsonPath('data.source', 'ai')
            ->assertJsonPath('data.categories.0.name', 'Clothes');

        $this->assertSame([['name' => 'Clothes']], Cache::get('smart_bag_ai_categories_en'));
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => !str_contains($request->url(), 'key=') && $request->hasHeader('x-goog-api-key'));
        $this->travel(1)->days();
        $this->assertNull(Cache::get('smart_bag_ai_categories_en'));
    }

    public function test_timeout_returns_fallback_and_queues_alert(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection timed out'));
        Log::spy();

        $response = $this->getJson('/api/smart-bags/ai/categories?language=ar');

        $response->assertOk()->assertExactJson([
            'success' => true,
            'data' => [
                'categories' => $this->expectedFallback('ar'),
                'language' => 'ar',
                'source' => 'fallback',
            ],
        ]);
        Log::shouldHaveReceived('error')->withArgs(fn ($message) => str_starts_with($message, 'AI Service Failed:'));
        Queue::assertPushed(SendAiFailureAlert::class);
    }

    public function test_exception_returns_fallback(): void
    {
        $this->mock(GeminiAIService::class)
            ->shouldReceive('generatePackingCategoriesFresh')
            ->once()
            ->andThrow(new \RuntimeException('AI unavailable'));

        $this->getJson('/api/smart-bags/ai/categories?language=ar')
            ->assertOk()->assertJsonPath('data.source', 'fallback');
    }

    public function test_rate_limit_returns_fallback_without_retry(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['error' => 'quota exceeded'], 429)]);
        $this->getJson('/api/smart-bags/ai/categories?language=ar')->assertOk()->assertJsonPath('data.source', 'fallback');
        Http::assertSentCount(1);
    }

    public function test_invalid_response_returns_fallback(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => '{"unexpected":true}']]]]],
        ])]);
        $this->getJson('/api/smart-bags/ai/categories?language=ar')->assertOk()->assertJsonPath('data.source', 'fallback');
    }

    public function test_cooldown_skips_second_ai_request(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['error' => 'quota exceeded'], 429)]);

        $this->getJson('/api/smart-bags/ai/categories?language=ar')->assertOk()->assertJsonPath('data.source', 'fallback');
        $this->getJson('/api/smart-bags/ai/categories?language=ar')->assertOk()->assertJsonPath('data.source', 'fallback');

        Http::assertSentCount(1);
        $this->assertTrue(Cache::has('smart_bag_ai_cooldown'));
    }

    public function test_alert_is_throttled_across_failures(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['error' => 'quota exceeded'], 429)]);

        $controller = app(BagController::class);
        $request = Request::create('/api/smart-bags/ai/categories?language=ar');
        $this->assertSame(200, $controller->getAICategories($request)->status());
        $this->assertTrue(Cache::has('smart_bag_ai_alert_throttle'));
        Cache::forget('smart_bag_ai_cooldown');
        $this->assertSame(200, $controller->getAICategories($request)->status());
        $this->assertTrue(Cache::has('smart_bag_ai_alert_throttle'));

        Http::assertSentCount(2);
        app()->terminate();
        Queue::assertPushed(SendAiFailureAlert::class, 1);
    }

    public function test_fallback_names_follow_language(): void
    {
        Cache::put('smart_bag_ai_cooldown', true, 60);

        $this->getJson('/api/smart-bags/ai/categories?language=ar')->assertOk()
            ->assertJsonPath('data.categories.0.name', 'الملابس والأحذية')
            ->assertJsonPath('data.language', 'ar');
        $this->getJson('/api/smart-bags/ai/categories?language=en')->assertOk()
            ->assertJsonPath('data.categories.0.name', 'clothing')
            ->assertJsonPath('data.language', 'en');
    }

    public function test_key_is_redacted_in_log_and_alert(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection failed at ?key=AIzaFAKE'));
        Log::spy();

        $this->getJson('/api/smart-bags/ai/categories?language=ar')->assertOk();

        Log::shouldHaveReceived('error')->once()->withArgs(fn ($message) =>
            str_contains($message, 'key=REDACTED') && !str_contains($message, 'AIzaFAKE'));
        Queue::assertPushed(SendAiFailureAlert::class, function ($job) {
            return str_contains($job->message, 'key=REDACTED') && !str_contains($job->message, 'AIzaFAKE');
        });
    }

    public function test_invalid_cached_categories_are_forgotten(): void
    {
        Cache::put('smart_bag_ai_categories_ar', [['invalid' => true]], now()->addDay());

        $this->getJson('/api/smart-bags/ai/categories?language=ar')->assertOk()->assertJsonPath('data.source', 'fallback');
        $this->assertNull(Cache::get('smart_bag_ai_categories_ar'));
    }

    public function test_suggest_items_failure_returns_ok_with_empty_items(): void
    {
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['error' => 'quota exceeded'], 429)]);

        $this->getJson('/api/smart-bags/ai/suggest-items?category=clothing&language=en')
            ->assertOk()->assertJsonPath('data.items', [])->assertJsonPath('data.source', 'fallback');
        Http::assertSentCount(1);
    }

    public function test_cache_hit_skips_ai(): void
    {
        Cache::put('smart_bag_ai_categories_ar', [['name' => 'Cached']], now()->addDay());
        $this->mock(GeminiAIService::class)->shouldNotReceive('generatePackingCategoriesFresh');

        $this->getJson('/api/smart-bags/ai/categories?language=ar')
            ->assertOk()->assertJsonPath('data.source', 'cache')
            ->assertJsonPath('data.categories.0.name', 'Cached');
    }

    private function expectedFallback(string $language): array
    {
        return array_map(function ($category) use ($language) {
            $category['name'] = $language === 'ar' ? $category['name_ar'] : $category['name'];
            return $category;
        }, config('smart_bag_ai.fallback_categories'));
    }
}
