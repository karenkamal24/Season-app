<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiAIService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash-exp');
        $this->apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Generate content using Gemini AI
     *
     * @param string $prompt
     * @param array $config
     * @return array
     * @throws Exception
     */
    public function generateContent(string $prompt, array $config = []): array
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout(25)
                ->retry(2, 500)
                ->post($this->apiUrl . '?key=' . $this->apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => array_merge([
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 8192,
                        'responseMimeType' => 'application/json',
                    ], $config),
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                    ]
                ]);

            if (!$response->successful()) {
                Log::error('Gemini API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Gemini API request failed: ' . $response->body());
            }

            $data = $response->json();
            
            $processingTime = (int)((microtime(true) - $startTime) * 1000);

            return [
                'text' => $data['candidates'][0]['content']['parts'][0]['text'] ?? '',
                'processing_time_ms' => $processingTime,
                'finish_reason' => $data['candidates'][0]['finishReason'] ?? 'UNKNOWN',
                'safety_ratings' => $data['candidates'][0]['safetyRatings'] ?? [],
            ];

        } catch (Exception $e) {
            Log::error('Gemini AI Service Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Extract JSON from AI response
     *
     * @param string $text
     * @return array
     * @throws Exception
     */
    public function extractJson(string $text): array
    {
        // Try to extract JSON from markdown code blocks
        if (preg_match('/```json\s*([\s\S]*?)\s*```/i', $text, $matches)) {
            $jsonText = $matches[1];
        } elseif (preg_match('/```\s*([\s\S]*?)\s*```/', $text, $matches)) {
            $jsonText = $matches[1];
        } else {
            $jsonText = $text;
        }

        // Clean up the text
        $jsonText = trim($jsonText);

        // Try to decode
        $decoded = json_decode($jsonText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to parse JSON from Gemini response', [
                'error' => json_last_error_msg(),
                'text' => $text
            ]);
            throw new Exception('Failed to parse JSON from AI response: ' . json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * Analyze bag with Gemini AI
     *
     * @param array $bagData
     * @return array
     * @throws Exception
     */
    public function analyzeBag(array $bagData): array
    {
        $prompt = $this->buildAnalysisPrompt($bagData);
        
        $response = $this->generateContent($prompt);
        
        $analysis = $this->extractJson($response['text']);
        
        // Add metadata
        $analysis['metadata'] = array_merge($analysis['metadata'] ?? [], [
            'analyzed_at' => now()->toIso8601String(),
            'ai_model' => $this->model,
            'processing_time_ms' => $response['processing_time_ms'],
            'finish_reason' => $response['finish_reason'],
        ]);

        return $analysis;
    }

    /**
     * Build analysis prompt for bag
     *
     * @param array $bagData
     * @return string
     */
    protected function buildAnalysisPrompt(array $bagData): string
    {
        $tripDetails = $bagData['tripDetails'] ?? [];
        $items = $bagData['items'] ?? [];
        $totalWeight = $bagData['totalWeight'] ?? 0;
        $preferences = $bagData['preferences'] ?? [];

        $itemsList = collect($items)->map(function ($item) {
            $essential = ($item['essential'] ?? false) ? '[ضروري]' : '';
            return "- {$item['name']} ({$item['weight']} كجم) - {$item['category']} {$essential}";
        })->join("\n");

        return <<<PROMPT
أنت مساعد ذكي متخصص في تنظيم حقائب السفر. مهمتك تحليل محتويات الحقيبة وتقديم اقتراحات ذكية.

## معلومات الرحلة:
- النوع: {$tripDetails['type']}
- المدة: {$tripDetails['duration']} أيام
- الوجهة: {$tripDetails['destination']}
- تاريخ المغادرة: {$tripDetails['departureDate']}
- الوزن الحالي: {$totalWeight} كجم
- الحد الأقصى: {$tripDetails['maxWeight']} كجم

## محتويات الحقيبة الحالية:
{$itemsList}

## المطلوب منك:

قدم تحليلاً كاملاً يشمل:

1. **الأغراض الناقصة** (missing_items):
   - اسم الغرض
   - الوزن المقدر
   - السبب (لماذا ناقص)
   - الأولوية (high/medium/low)
   - الفئة

2. **الأغراض الزائدة** (extra_items):
   - اسم الغرض (من القائمة الموجودة)
   - السبب (لماذا غير ضروري)
   - الوزن الذي سيتم توفيره

3. **تحسينات الوزن** (weight_optimization):
   - الوزن الحالي
   - الوزن المقترح
   - الوزن الموفر
   - التأثير (high/medium/low)

4. **اقتراحات إضافية** (additional_suggestions):
   - إعادة توزيع الأغراض
   - نصائح عامة

5. **تنبيه ذكي** (smart_alert):
   - الوقت المتبقي للرحلة
   - الرسالة
   - الإجراء المقترح
   - مستوى الأهمية

## قواعد مهمة:
- ✅ كن محدداً في الأسباب
- ✅ راعي نوع الرحلة (رحلة عمل تحتاج ملابس رسمية)
- ✅ راعي مدة السفر (كل يوم يحتاج ملابس)
- ✅ راعي المناخ في الوجهة
- ✅ اقترح بدائل أخف وزناً
- ❌ لا تقترح أغراض غالية جداً
- ❌ لا تقترح حذف أغراض ضرورية

**يجب أن يكون الرد بصيغة JSON فقط، بدون أي نص إضافي:**

```json
{
  "analysis_id": "unique_id",
  "missing_items": [
    {
      "id": "missing_1",
      "name": "اسم الغرض",
      "weight": 0.5,
      "reason": "السبب",
      "priority": "high",
      "category": "الفئة"
    }
  ],
  "extra_items": [
    {
      "id": "extra_1",
      "item_id_in_bag": "item_id",
      "name": "اسم الغرض",
      "reason": "السبب",
      "weight_saved": 1.5
    }
  ],
  "weight_optimization": {
    "current_weight": {$totalWeight},
    "suggested_weight": 0,
    "weight_saved": 0,
    "impact_level": "high",
    "percentage_saved": 0,
    "suggestions": []
  },
  "additional_suggestions": [
    {
      "id": "sugg_1",
      "category": "organization",
      "title": "العنوان",
      "description": "الوصف",
      "priority": "medium"
    }
  ],
  "smart_alert": {
    "alert_id": "alert_1",
    "time_remaining": "X ساعات",
    "time_remaining_minutes": 0,
    "message": "الرسالة",
    "action": "الإجراء",
    "severity": "high",
    "icon": "clock"
  },
  "metadata": {
    "confidence_score": 0.92
  }
}
```
PROMPT;
    }
}

