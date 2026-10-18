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

        // الكاش لمدة 24 ساعة (86400 ثانية) بدلاً من 12 ساعة لتحسين السرعة
        return Cache::remember($cacheKey, 86400, function () use ($country, $language, $backupKey) {
            try {
                $eventsData = $this->fetchEventsFromGemini($country, $language);
                
                // حفظ نسخة احتياطية دائمة في الكاش للرجوع إليها في حال انقطاع الخدمة أو بطء الـ API
                if (!empty($eventsData['events'])) {
                    Cache::forever($backupKey, $eventsData);
                }

                return $eventsData;
            } catch (Exception $e) {
                // إذا حدث خطأ أو Timeout في Gemini ولكن توجد نسخة كاش احتياطية سابقة، نعيدها فوراً
                $backup = Cache::get($backupKey);
                if ($backup && is_array($backup)) {
                    Log::warning("Gemini API failed/timed out, serving backup events cache for {$country} ({$language})", [
                        'error' => $e->getMessage(),
                    ]);
                    return $backup;
                }

                throw $e;
            }
        });
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

        // برومبت سريع ومباشر يطلب 6-10 أحداث فقط لتقليل عدد الـ Tokens ووقت التوليد لأقصى درجة
        $prompt = "You are an assistant that finds real-world upcoming events happening in a specific country.

INPUT:
1) \"language\": \"{$language}\"
2) \"country\": \"{$country}\"
3) \"today_date\": \"{$today}\"

TASK:
1. Search for upcoming events happening in the specified country starting from 'today_date'.
2. Return ONLY the nearest upcoming events (between 6 to 10 events), sorted from the closest date to the farthest.
3. For each event, provide:
    - \"title\": event name
    - \"date\": YYYY-MM-DD
    - \"start_at\": HH:MM or YYYY-MM-DD HH:MM or null
    - \"end_at\": HH:MM or YYYY-MM-DD HH:MM or null
    - \"city\": city name
    - \"venue\": venue name or null
    - \"country\": \"{$country}\"
    - \"category\": type (concert, conference, exhibition, sports, workshop, festival, etc.)
    - \"source\": platform or official website name

Do not include a 'url' field.

LANGUAGE RULES:
- If language is \"en\": ALL text fields MUST be in English only.
- If language is \"ar\": ALL text fields MUST be in Arabic when available (keep English name if no translation exists).

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
            \"start_at\": null,
            \"end_at\": null,
            \"city\": \"...\",
            \"venue\": \"...\",
            \"country\": \"{$country}\",
            \"category\": \"...\",
            \"source\": \"...\"
        }
    ]
}

If no events are found, return the same structure with empty events array and \"note\": \"No upcoming events found\".";

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
                    'maxOutputTokens' => 2048, // تقليل التوكنز لتسريع الاستجابة ومنع التعليق
                    'responseMimeType' => 'application/json',
                ],
            ];

            // تحسين الاتصال: خفض timeout إلى 10 ثوان، تسريع الـ DNS عبر IPv4
            $response = Http::timeout(10)
                ->connectTimeout(4)
                ->retry(1, 150)
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    ],
                ])
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

            $responseText = $data['candidates'][0]['content']['parts'][0]['text'];

            // حذف علامات تنسيق Markdown للـ JSON (مثل ```json)
            $responseText = preg_replace('/^```json\s*/i', '', $responseText);
            $responseText = preg_replace('/^```\s*/', '', $responseText);
            $responseText = preg_replace('/\s*```$/', '', $responseText);
            $responseText = trim($responseText);

            // تحليل JSON
            $jsonData = json_decode($responseText, true);

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
}
