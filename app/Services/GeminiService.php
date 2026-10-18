<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Client\ConnectionException;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model');
        $this->baseUrl = config('services.gemini.base_url');
    }

    /**
     * Search/Query Gemini AI
     *
     * @param string $query
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function search(string $query, array $options = []): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured');
        }

        try {
            $url = "{$this->baseUrl}/models/{$this->model}:generateContent";

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $query
                            ]
                        ]
                    ]
                ],
            ];

            // Add optional parameters
            if (isset($options['temperature'])) {
                $payload['generationConfig']['temperature'] = $options['temperature'];
            }

            if (isset($options['maxOutputTokens'])) {
                $payload['generationConfig']['maxOutputTokens'] = $options['maxOutputTokens'];
            }

            if (isset($options['topP'])) {
                $payload['generationConfig']['topP'] = $options['topP'];
            }

            if (isset($options['topK'])) {
                $payload['generationConfig']['topK'] = $options['topK'];
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("{$url}?key={$this->apiKey}", $payload);

            if ($response->failed()) {
                $error = $response->json();
                Log::error('Gemini API Error - Search', [
                    'status' => $response->status(),
                    'error' => $error,
                ]);
                throw new Exception('Gemini API request failed: ' . ($error['error']['message'] ?? 'Unknown API error'));
            }

            $data = $response->json();

            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                // قد تكون الاستجابة غير مكتملة أو محظورة
                Log::error('Gemini Invalid Response Structure - Search', ['response_data' => $data]);
                throw new Exception('Invalid or incomplete response from Gemini API');
            }

            return [
                'text' => $data['candidates'][0]['content']['parts'][0]['text'],
                'model' => $this->model,
                'usage' => $data['usageMetadata'] ?? null,
            ];
        } catch (ConnectionException $e) {
            // خطأ شبكة أو Timeout
            Log::error('Gemini Connection Error - Search', ['message' => $e->getMessage()]);
            throw new Exception('Connection failed while reaching Gemini API: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Gemini Service General Error - Search', [
                'message' => $e->getMessage(),
                'query' => $query,
            ]);
            throw $e;
        }
    }

    /**
     * Search for upcoming events in a specific country
     *
     * @param string $country
     * @param string $language
     * @return array
     * @throws Exception
     */
    public function searchUpcomingEvents(string $country, string $language = 'en', bool $forceRefresh = false): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured');
        }

        // Cache key موحد وآمن بدون مسافات أو حروف خاصة
        $countrySlug = Str::slug($country, '_') ?: 'default';
        $cacheKey = "gemini_events_{$countrySlug}_{$language}";
        $backupKey = "gemini_events_backup_{$countrySlug}_{$language}";

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        // إذا كان هناك كاش صالح وغير فارغ، نرجعه فوراً
        $cached = Cache::get($cacheKey);
        if ($cached && !empty($cached['events']) && !$forceRefresh) {
            return $cached;
        }

        // إذا كان الكاش القديم فارغاً، نحذفه
        if ($cached && empty($cached['events'])) {
            Cache::forget($cacheKey);
        }

        // إذا كان المطلوب بالإنجليزية، نعتمد على الفعاليات العربية ونترجمها لتطابق الفعاليات والتواريخ 100%
        if ($language === 'en') {
            $arCacheKey = "gemini_events_{$countrySlug}_ar";
            $arData = Cache::get($arCacheKey);

            // إذا لم تكن الفعاليات العربية موجودة بالكاش، نجلبها أولاً
            if (!$arData || empty($arData['events'])) {
                try {
                    $arData = $this->searchUpcomingEvents($country, 'ar', $forceRefresh);
                } catch (\Throwable $e) {
                    Log::warning("Fetching Arabic events before translating to English failed: " . $e->getMessage());
                }
            }

            if (!empty($arData['events'])) {
                try {
                    $translatedData = $this->translateEventsToEnglish($arData, $country);
                    if (!empty($translatedData['events'])) {
                        Cache::put($cacheKey, $translatedData, 86400);
                        Cache::forever($backupKey, $translatedData);
                        return $translatedData;
                    }
                } catch (\Throwable $e) {
                    Log::warning("Translating Arabic events to English failed, falling back to direct generation: " . $e->getMessage());
                }
            }
        }

        try {
            $eventsData = $this->fetchEventsFromGemini($country, $language);

            // نخزن في الكاش إذا رجعت فعاليات
            if (!empty($eventsData['events'])) {
                Cache::put($cacheKey, $eventsData, 86400); // 24 ساعة
                Cache::forever($backupKey, $eventsData);
            }

            return $eventsData;
        } catch (Exception $e) {
            // في حال حدوث خطأ أو Timeout في Gemini، نرجع آخر كاش محفوظ لنفس الدولة إن وُجد
            $backup = Cache::get($backupKey);
            if ($backup && !empty($backup['events'])) {
                Log::warning("Gemini failed, serving last cached events for {$country} ({$language})", [
                    'error' => $e->getMessage(),
                ]);
                return $backup;
            }

            throw $e;
        }
    }

    /**
     * Fetch events from Gemini API (internal method)
     *
     * @param string $country
     * @param string $language
     * @return array
     * @throws Exception
     */
    private function fetchEventsFromGemini(string $country, string $language = 'en'): array
    {
        $today = date('Y-m-d');
        $currentYear = date('Y');
        $nextYear = (int)$currentYear + 1;

        $prompt = "You are a tourism and culture expert assistant.

TASK:
Provide a curated list of the top 6 to 10 prominent annual, seasonal, cultural, tourist, and entertainment events and festivals in {$country}.
For example:
- In Saudi Arabia: Riyadh Season, Jeddah Season, Saudi Arabian F1 Grand Prix, LEAP Conference, Riyadh International Book Fair, The Saudi Cup.
- In Egypt: Cairo International Book Fair, Cairo International Film Festival, New Alamein Festival, Citadel Music Festival, Arab Music Festival.
- For any other country: their top famous national and international festivals, seasons, and exhibitions.

CRITICAL RULES:
1. Return between 6 to 10 events.
2. DATE CONSTRAINT: Today's reference date is {$today}. All event dates MUST be upcoming dates in {$currentYear} or {$nextYear} (e.g. 2025/2026). NEVER return old dates from 2024 or earlier.
3. The 'generated_at' field MUST be exactly '{$today}'.
4. If language is \"ar\", write title, city, venue, category, and source in Arabic. If \"en\", write in English.
5. DO NOT return an empty list.

OUTPUT FORMAT:
Respond with pure JSON object matching this structure:
{
    \"country\": \"{$country}\",
    \"language\": \"{$language}\",
    \"generated_at\": \"{$today}\",
    \"events\": [
        {
            \"title\": \"...\",
            \"date\": \"YYYY-MM-DD\",
            \"start_at\": \"18:00\",
            \"end_at\": \"23:00\",
            \"city\": \"...\",
            \"venue\": \"...\",
            \"country\": \"{$country}\",
            \"category\": \"...\",
            \"source\": \"...\"
        }
    ]
}";

        try {
            $url = "{$this->baseUrl}/models/{$this->model}:generateContent";

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.2, // خفض الحرارة لتوليد أسرع وأكثر دقة وتفادي البطء
                    'maxOutputTokens' => 4096, // رفع التوكنز لتفادي انقطاع نصوص الـ UTF-8 العربية في منتصف الـ JSON
                    'responseMimeType' => 'application/json',
                ],
            ];

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("{$url}?key={$this->apiKey}", $payload);

            if ($response->failed()) {
                $error = $response->json();
                Log::error('Gemini API Error - Events Search', [
                    'status' => $response->status(),
                    'error' => $error,
                    'country' => $country,
                    'language' => $language,
                ]);
                throw new Exception('Gemini API request failed: ' . ($error['error']['message'] ?? 'Unknown API error'));
            }

            $data = $response->json();

            if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                 Log::error('Gemini Invalid Response Structure - Events Search', ['response_data' => $data]);
                 throw new Exception('Invalid or incomplete response from Gemini API');
            }

            $responseText = trim($data['candidates'][0]['content']['parts'][0]['text']);

            // استخراج نص الـ JSON بدقة متناهية بين أول { وآخر } لتفادي أي markdown أو نصوص إضافية
            $start = strpos($responseText, '{');
            $end = strrpos($responseText, '}');
            if ($start !== false && $end !== false && $end > $start) {
                $cleanJson = substr($responseText, $start, $end - $start + 1);
            } else {
                $cleanJson = $responseText;
            }

            // تحليل JSON
            $jsonData = json_decode($cleanJson, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($jsonData)) {
                Log::error('Gemini Events Response Parse Error', [
                    'response_snippet' => substr($responseText, 0, 500),
                    'response_length' => strlen($responseText),
                    'json_error' => json_last_error_msg(),
                ]);

                return [
                    'country' => $country,
                    'language' => $language,
                    'generated_at' => date('Y-m-d'),
                    'events' => [],
                    'note' => 'No upcoming events found'
                ];
            }

            return $jsonData;
        } catch (ConnectionException $e) {
            Log::error('Gemini Connection Error - Events Search', ['message' => $e->getMessage(), 'country' => $country]);
            throw new Exception('Connection failed while reaching Gemini API: ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error('Gemini Service General Error - Events Search', [
                'message' => $e->getMessage(),
                'country' => $country,
                'language' => $language,
            ]);
            throw $e;
        }
    }

    /**
     * Translate Arabic events to English while strictly preserving exact dates, times, and order.
     *
     * @param array $arData
     * @param string $country
     * @return array
     * @throws Exception
     */
    private function translateEventsToEnglish(array $arData, string $country): array
    {
        $events = $arData['events'] ?? [];
        if (empty($events)) {
            return $arData;
        }

        $eventsJson = json_encode($events, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $generatedAt = $arData['generated_at'] ?? date('Y-m-d');

        $prompt = "You are a professional translator and cultural editor.
Translate the following list of events from Arabic to English.

CRITICAL INSTRUCTIONS:
1. Translate ONLY the Arabic text fields: 'title', 'city', 'venue', 'category', 'source'.
2. DO NOT change or alter any dates or times ('date', 'start_at', 'end_at') or 'country'. Keep them EXACTLY as they are in the source.
3. Keep the exact same number of events and in the exact same sequence.
4. Return pure JSON matching this exact structure:
{
    \"country\": \"{$country}\",
    \"language\": \"en\",
    \"generated_at\": \"{$generatedAt}\",
    \"events\": [
        {
            \"title\": \"...\",
            \"date\": \"...\",
            \"start_at\": \"...\",
            \"end_at\": \"...\",
            \"city\": \"...\",
            \"venue\": \"...\",
            \"country\": \"{$country}\",
            \"category\": \"...\",
            \"source\": \"...\"
        }
    ]
}

Events to translate:
{$eventsJson}";

        $url = "{$this->baseUrl}/models/{$this->model}:generateContent";
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 4096,
                'responseMimeType' => 'application/json',
            ],
        ];

        $response = Http::timeout(30)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$url}?key={$this->apiKey}", $payload);

        if ($response->failed()) {
            throw new Exception("Gemini Translation API call failed: " . $response->body());
        }

        $data = $response->json();
        $responseText = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');
        $start = strpos($responseText, '{');
        $end = strrpos($responseText, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $cleanJson = substr($responseText, $start, $end - $start + 1);
        } else {
            $cleanJson = $responseText;
        }

        $translated = json_decode($cleanJson, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($translated) && !empty($translated['events'])) {
            return $translated;
        }

        throw new Exception("Failed to parse translated events JSON: " . json_last_error_msg());
    }

    /**
     * Direct live diagnostic test of Gemini API without cache
     */
    public function debugGeminiCall(string $country, string $language = 'en'): array
    {
        $today = date('Y-m-d');
        $url = "{$this->baseUrl}/models/{$this->model}:generateContent";

        $prompt = "You are a tourism expert. List 4 real annual events in {$country}. Return pure JSON with country, language, and events list.";

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature' => 0.2,
                'maxOutputTokens' => 2048,
                'responseMimeType' => 'application/json',
            ],
        ];

        try {
            $response = Http::timeout(10)->post("{$url}?key={$this->apiKey}", $payload);

            return [
                'model' => $this->model,
                'key_preview' => substr($this->apiKey, 0, 10) . '...',
                'http_status' => $response->status(),
                'http_successful' => $response->successful(),
                'google_response' => $response->json(),
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'model' => $this->model,
                'key_preview' => substr($this->apiKey, 0, 10) . '...',
            ];
        }
    }
}


