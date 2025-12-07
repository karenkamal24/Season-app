<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
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
    public function searchUpcomingEvents(string $country, string $language = 'en'): array
    {
        if (empty($this->apiKey)) {
            throw new Exception('Gemini API key is not configured');
        }

        // 💡 التعديل 1: تحديد تاريخ اليوم الحالي لضمان البحث عن أحداث مستقبلية
        $today = date('Y-m-d');

        $prompt = "You are an assistant that finds real-world upcoming events happening in a specific country.

INPUT:
You will receive three variables:
1) \"language\": either \"ar\" for Arabic or \"en\" for English.
2) \"country\": the country name in English (e.g., \"Egypt\", \"Saudi Arabia\", \"UAE\", \"France\", etc.).
3) \"today_date\": The current date in YYYY-MM-DD format.

TASK:
1. Search for upcoming events happening in the specified country **starting from the 'today_date'**.
2. Return ONLY the nearest upcoming events (preferably the next 10–20 events), sorted from the closest date to the farthest.
3. For each event, provide the following fields:
    - \"title\": the name of the event
    - \"date\": full date in ISO 8601 format (YYYY-MM-DD)
    - \"start_at\": event start time or start datetime. Use:
        • HH:MM if time only is known
        • YYYY-MM-DD HH:MM if date and time are known
        • null if unknown
    - \"end_at\": event end time or end datetime. Use same rules as start_at
    - \"city\": the city where the event happens
    - \"venue\": place/venue name if available, otherwise null
    - \"country\": same value as the input country header
    - \"category\": type of event (concert, conference, exhibition, sports, workshop, festival, etc.) if possible
    - \"source\": the website, platform, or service where the event information was found

**NOTE: Do not include a 'url' field.**

LANGUAGE RULES (CRITICAL - MUST FOLLOW):
- If the \"language\" variable is \"en\" (English):
    • You MUST return ALL text fields (title, city, venue, category, source) in English ONLY.
    • Do NOT use Arabic text in any field.
    • Example: title should be \"Cairo International Film Festival\" NOT \"مهرجان القاهرة السينمائي\"
- If the \"language\" variable is \"ar\" (Arabic):
    • You MUST return ALL text fields (title, city, venue, category, source) in Arabic when available.
    • If no Arabic translation exists, keep the original English name.
- The \"country\" field in each event should match the input country name exactly.
- The \"language\" field in the response MUST match the input language exactly (\"en\" or \"ar\").

OUTPUT FORMAT:
Always respond with a single JSON object in this structure:

{
    \"country\": \"<country from header>\",
    \"language\": \"<language from header>\",
    \"generated_at\": \"<current date in YYYY-MM-DD>\",
    \"events\": [
        {
            \"title\": \"...\",
            \"date\": \"YYYY-MM-DD\",
            \"start_at\": \"YYYY-MM-DD HH:MM\" or \"HH:MM\" or null,
            \"end_at\": \"YYYY-MM-DD HH:MM\" or \"HH:MM\" or null,
            \"city\": \"...\",
            \"venue\": \"...\",
            \"country\": \"...\",
            \"category\": \"...\",
            \"source\": \"...\" // تم حذف url
        }
    ]
}

RULES:
- You MUST return pure JSON only. No markdown, no explanations, no introductory or closing text.
- Do NOT include any fields that are not specified.
- If no events are found, return:
    • the same JSON structure
    • an empty \"events\" array
    • and add a field \"note\": \"No upcoming events found\"

Now, find upcoming events for:
- Country: {$country}
- Language: {$language}
- Today's Date: {$today}

Return ONLY the JSON response, nothing else.";

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
                    'temperature' => 0.7,
                    'maxOutputTokens' => 8192,
                    'responseMimeType' => 'application/json',
                ],
            ];

            // زيادة مهلة الاتصال والرد إلى 30 ثانية
            $response = Http::timeout(30)->withHeaders([
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
            $responseText = preg_replace('/^```json\s*/', '', $responseText);
            $responseText = preg_replace('/^```\s*/', '', $responseText);
            $responseText = preg_replace('/\s*```$/', '', $responseText);
            $responseText = trim($responseText);

            // محاولة تحليل JSON
            $jsonData = json_decode($responseText, true);

            // في حال فشل التحليل نهائياً، أرجع استجابة بديلة مع تسجيل الخطأ
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($jsonData)) {
                Log::error('Gemini Events Response Parse Error', [
                    'response_snippet' => substr($responseText, 0, 500),
                    'response_length' => strlen($responseText),
                    'json_error' => json_last_error_msg(),
                    'json_error_code' => json_last_error(),
                ]);

                // إرجاع استجابة بديلة بدلاً من طرح خطأ 500
                return [
                    'country' => $country,
                    'language' => $language,
                    'generated_at' => date('Y-m-d'),
                    'events' => [],
                    'note' => 'No upcoming events found',
                    'error' => 'Failed to parse response from Gemini API, check logs for details.'
                ];
            }

            return $jsonData;
        } catch (ConnectionException $e) {
            // خطأ شبكة أو Timeout
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
