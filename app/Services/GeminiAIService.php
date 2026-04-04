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
            $response = Http::timeout(15)
                ->retry(1, 200)
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
                        'maxOutputTokens' => 2048,
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
        if (preg_match('/```json\s*([\s\S]*?)\s*```/i', $text, $matches)) {
            $jsonText = $matches[1];
        } elseif (preg_match('/```\s*([\s\S]*?)\s*```/', $text, $matches)) {
            $jsonText = $matches[1];
        } else {
            $jsonText = $text;
        }

        $jsonText = trim($jsonText);

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

        $response = $this->generateContent($prompt, [
            'maxOutputTokens' => 2048,
            'temperature' => 0.5,
        ]);

        $analysis = $this->extractJson($response['text']);

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
أنت مساعد ذكي لتنظيم حقائب السفر. حلل الحقيبة وأعد JSON فقط بدون أي نص إضافي.

رحلة: {$tripDetails['type']} - {$tripDetails['duration']} أيام - {$tripDetails['destination']} - {$tripDetails['departureDate']}
وزن: {$totalWeight}/{$tripDetails['maxWeight']} كجم

الأغراض الحالية:
{$itemsList}

القواعد:
- راعي نوع الرحلة والمدة والمناخ
- اقترح بدائل أخف وزناً
- لا تحذف أغراض ضرورية

أعد JSON بهذه المفاتيح فقط:
- missing_items: [{id, name, weight, reason, priority(high/medium/low), category}]
- extra_items: [{id, item_id_in_bag, name, reason, weight_saved}]
- weight_optimization: {current_weight, suggested_weight, weight_saved, impact_level, percentage_saved, suggestions}
- additional_suggestions: [{id, category, title, description, priority}]
- smart_alert: {alert_id, time_remaining, time_remaining_minutes, message, action, severity, icon}
- metadata: {confidence_score}
PROMPT;
    }

    /**
     * Generate packing categories using Gemini AI
     *
     * @param string $language Language code (ar/en)
     * @return array
     * @throws Exception
     */
    public function generatePackingCategories(string $language = 'ar'): array
    {
        $prompt = $this->buildCategoriesPrompt($language);

        $response = $this->generateContent($prompt, [
            'maxOutputTokens' => 512,
            'temperature' => 0.3,
        ]);

        $categories = $this->extractJson($response['text']);

        if (!is_array($categories)) {
            throw new Exception('Invalid response format from AI');
        }

        if (isset($categories['categories'])) {
            $categories = $categories['categories'];
        }

        return $categories;
    }

    /**
     * Suggest items for a category using Gemini AI
     *
     * @param string $category Category name
     * @param string $language Language code (ar/en)
     * @return array
     * @throws Exception
     */
    public function suggestItemsForCategory(string $category, string $language = 'ar'): array
    {
        $prompt = $this->buildItemsPrompt($category, $language);

        $response = $this->generateContent($prompt, [
            'maxOutputTokens' => 1024,
            'temperature' => 0.4,
        ]);

        $items = $this->extractJson($response['text']);

        if (!is_array($items)) {
            throw new Exception('Invalid response format from AI');
        }

        if (isset($items['items'])) {
            $items = $items['items'];
        }

        return $items;
    }

    /**
     * Build prompt for generating packing categories
     *
     * @param string $language Language code (ar/en)
     * @return string
     */
    protected function buildCategoriesPrompt(string $language): string
    {
        if ($language === 'en') {
            return <<<PROMPT
Generate 8-10 essential packing categories for travel.
Respond in English language.
Return as JSON array: [{"name": "category_name"}]

Example format:
[
  {"name": "Clothing"},
  {"name": "Toiletries"},
  {"name": "Electronics"},
  {"name": "Documents"},
  {"name": "Medications"},
  {"name": "Accessories"},
  {"name": "Food & Snacks"},
  {"name": "Entertainment"}
]

Return ONLY valid JSON array, no additional text.
PROMPT;
        } else {
            return <<<PROMPT
قم بتوليد 8-10 فئات أساسية لتعبئة الحقائب للسفر.
الرد باللغة العربية.
قم بإرجاع مصفوفة JSON: [{"name": "اسم_الفئة"}]

مثال على التنسيق:
[
  {"name": "الملابس"},
  {"name": "مستلزمات النظافة"},
  {"name": "الإلكترونيات"},
  {"name": "المستندات"},
  {"name": "الأدوية"},
  {"name": "الإكسسوارات"},
  {"name": "الطعام والوجبات الخفيفة"},
  {"name": "الترفيه"}
]

قم بإرجاع مصفوفة JSON صالحة فقط، بدون أي نص إضافي.
PROMPT;
        }
    }

    /**
     * Build prompt for suggesting items for a category
     *
     * @param string $category Category name
     * @param string $language Language code (ar/en)
     * @return string
     */
    protected function buildItemsPrompt(string $category, string $language): string
    {
        if ($language === 'en') {
            return <<<PROMPT
Suggest 10-15 essential items for '{$category}' category when packing for travel.
For each item provide estimated weight in grams.
Respond in English language.
Return as JSON: [{"name": "item_name", "weight": weight_in_grams}]

Example format:
[
  {"name": "T-Shirt", "weight": 150},
  {"name": "Jeans", "weight": 500},
  {"name": "Underwear", "weight": 50},
  {"name": "Socks", "weight": 40}
]

Important:
- Weight should be in grams (not kilograms)
- Provide realistic weight estimates
- Focus on essential travel items
- Return ONLY valid JSON array, no additional text
PROMPT;
        } else {
            return <<<PROMPT
اقترح 10-15 عنصراً أساسياً لفئة '{$category}' عند تعبئة الحقائب للسفر.
لكل عنصر، قدم الوزن المقدر بالجرام.
الرد باللغة العربية.
قم بإرجاع JSON: [{"name": "اسم_العنصر", "weight": الوزن_بالجرام}]

مثال على التنسيق:
[
  {"name": "قميص", "weight": 150},
  {"name": "بنطال", "weight": 500},
  {"name": "ملابس داخلية", "weight": 50},
  {"name": "جوارب", "weight": 40}
]

مهم:
- الوزن يجب أن يكون بالجرام (وليس بالكيلوجرام)
- قدم تقديرات وزن واقعية
- ركز على العناصر الأساسية للسفر
- قم بإرجاع مصفوفة JSON صالحة فقط، بدون أي نص إضافي
PROMPT;
        }
    }

    /**
     * Estimate weight for a custom item using Gemini AI
     *
     * @param string $itemName Item name
     * @param string $language Language code (ar/en)
     * @return float Estimated weight in kilograms
     * @throws Exception
     */
    public function estimateItemWeight(string $itemName, string $language = 'ar'): float
    {
        $prompt = $this->buildWeightEstimationPrompt($itemName, $language);

        $response = $this->generateContent($prompt, [
            'maxOutputTokens' => 256,
            'temperature' => 0.2,
        ]);

        $result = $this->extractJson($response['text']);

        $weight = null;
        if (isset($result['weight'])) {
            $weight = (float) $result['weight'];
        } elseif (isset($result['weight_kg'])) {
            $weight = (float) $result['weight_kg'];
        } elseif (is_numeric($result)) {
            $weight = (float) $result;
        } else {
            throw new Exception('Invalid weight format from AI response');
        }

        if ($weight > 1000) {
            $weight = $weight / 1000;
        }

        if ($weight < 0.001 || $weight > 100) {
            throw new Exception('Weight estimate out of reasonable range');
        }

        return round($weight, 3);
    }

    /**
     * Build prompt for estimating item weight
     *
     * @param string $itemName Item name
     * @param string $language Language code (ar/en)
     * @return string
     */
    protected function buildWeightEstimationPrompt(string $itemName, string $language): string
    {
        if ($language === 'en') {
            return <<<PROMPT
Estimate the weight of the following travel item: "{$itemName}"

Provide a realistic weight estimate in kilograms (kg) for this item when packed for travel.
Consider:
- Typical size and material
- Standard travel version of this item
- Average weight for common travel items

Respond with ONLY a JSON object in this format:
{
  "weight": weight_in_kg,
  "unit": "kg",
  "confidence": "high|medium|low"
}

Example:
{
  "weight": 0.5,
  "unit": "kg",
  "confidence": "high"
}

Important:
- Weight must be in kilograms (not grams)
- Provide realistic estimates (typically 0.01 to 50 kg for travel items)
- Return ONLY valid JSON, no additional text
PROMPT;
        } else {
            return <<<PROMPT
قدر وزن العنصر التالي للسفر: "{$itemName}"

قدم تقديراً واقعياً للوزن بالكيلوجرام (kg) لهذا العنصر عند تعبئته للسفر.
راعي:
- الحجم النموذجي والمادة
- النسخة القياسية للسفر من هذا العنصر
- الوزن المتوسط للعناصر الشائعة للسفر

قم بالرد بـ JSON فقط بالتنسيق التالي:
{
  "weight": الوزن_بالكيلوجرام,
  "unit": "kg",
  "confidence": "high|medium|low"
}

مثال:
{
  "weight": 0.5,
  "unit": "kg",
  "confidence": "high"
}

مهم:
- الوزن يجب أن يكون بالكيلوجرام (وليس بالجرام)
- قدم تقديرات واقعية (عادة من 0.01 إلى 50 كجم للعناصر السفر)
- قم بإرجاع JSON صالح فقط، بدون أي نص إضافي
PROMPT;
        }
    }
}
