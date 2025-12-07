<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GeminiEventsApiTest extends TestCase
{
    /**
     * Test successful events search with EGY country code
     */
    public function test_get_events_with_egy_country_code(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'ar',
            'Accept-Country' => 'EGY',
        ])->getJson('/api/gemini/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'country',
                'language',
                'generated_at',
                'events' => [
                    '*' => [
                        'title',
                        'date',
                        'start_at',
                        'end_at',
                        'city',
                        'venue',
                        'country',
                        'category',
                        'source',
                    ]
                ]
            ]);
    }

    /**
     * Test successful events search with KSA country code
     */
    public function test_get_events_with_ksa_country_code(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'en',
            'Accept-Country' => 'KSA',
        ])->getJson('/api/gemini/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'country',
                'language',
                'generated_at',
                'events',
            ]);
    }

    /**
     * Test events search with query parameter
     */
    public function test_get_events_with_query_parameter(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'ar',
        ])->getJson('/api/gemini/events?country=UAE');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'country',
                'language',
                'generated_at',
                'events',
            ]);
    }

    /**
     * Test events search with backward compatible headers
     */
    public function test_get_events_with_old_headers(): void
    {
        $response = $this->withHeaders([
            'language' => 'ar',
            'country' => 'EGY',
        ])->getJson('/api/gemini/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'country',
                'language',
                'generated_at',
                'events',
            ]);
    }

    /**
     * Test missing country parameter returns 400
     */
    public function test_get_events_without_country_returns_400(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'ar',
        ])->getJson('/api/gemini/events');

        $response->assertStatus(400)
            ->assertJsonStructure([
                'status',
                'message',
                'meta',
                'data',
            ]);
    }

    /**
     * Test default language is Arabic
     */
    public function test_default_language_is_arabic(): void
    {
        $response = $this->getJson('/api/gemini/events?country=EGY');

        $response->assertStatus(200)
            ->assertJson([
                'language' => 'ar',
            ]);
    }

    /**
     * Test English language support
     */
    public function test_english_language_support(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'en',
            'Accept-Country' => 'EGY',
        ])->getJson('/api/gemini/events');

        $response->assertStatus(200)
            ->assertJson([
                'language' => 'en',
            ]);
    }

    /**
     * Test invalid language defaults to Arabic
     */
    public function test_invalid_language_defaults_to_arabic(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr',
            'Accept-Country' => 'EGY',
        ])->getJson('/api/gemini/events');

        $response->assertStatus(200)
            ->assertJson([
                'language' => 'ar',
            ]);
    }

    /**
     * Test POST request works
     */
    public function test_post_request_works(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'ar',
            'Accept-Country' => 'EGY',
        ])->postJson('/api/gemini/events');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'country',
                'language',
                'generated_at',
                'events',
            ]);
    }

    /**
     * Test country code conversion (EGY -> Egypt)
     */
    public function test_country_code_conversion(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'ar',
            'Accept-Country' => 'EGY',
        ])->getJson('/api/gemini/events');

        $response->assertStatus(200);

        // The response should contain country name, not code
        $data = $response->json();
        $this->assertIsString($data['country']);
        $this->assertNotEquals('EGY', $data['country']);
    }

    /**
     * Test multiple country codes
     */
    public function test_multiple_country_codes(): void
    {
        $countries = ['EGY', 'KSA', 'UAE', 'JOR'];

        foreach ($countries as $country) {
            $response = $this->withHeaders([
                'Accept-Language' => 'ar',
                'Accept-Country' => $country,
            ])->getJson('/api/gemini/events');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'country',
                    'language',
                    'generated_at',
                    'events',
                ]);
        }
    }

    /**
     * Test response contains generated_at in correct format
     */
    public function test_generated_at_format(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'ar',
            'Accept-Country' => 'EGY',
        ])->getJson('/api/gemini/events');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertArrayHasKey('generated_at', $data);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $data['generated_at']);
    }

    /**
     * Test events array structure
     */
    public function test_events_array_structure(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'ar',
            'Accept-Country' => 'EGY',
        ])->getJson('/api/gemini/events');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertIsArray($data['events']);

        if (count($data['events']) > 0) {
            $event = $data['events'][0];
            $this->assertArrayHasKey('title', $event);
            $this->assertArrayHasKey('date', $event);
            $this->assertArrayHasKey('city', $event);
            $this->assertArrayHasKey('country', $event);
            $this->assertArrayHasKey('category', $event);
            $this->assertArrayHasKey('source', $event);
        }
    }

    /**
     * Test empty events response structure
     */
    public function test_empty_events_response_structure(): void
    {
        // This test might pass or fail depending on Gemini response
        // It's mainly to ensure the structure is correct
        $response = $this->withHeaders([
            'Accept-Language' => 'ar',
            'Accept-Country' => 'EGY',
        ])->getJson('/api/gemini/events');

        $response->assertStatus(200);

        $data = $response->json();
        $this->assertIsArray($data['events']);

        // If no events, should have note field
        if (count($data['events']) === 0) {
            $this->assertArrayHasKey('note', $data);
        }
    }
}

