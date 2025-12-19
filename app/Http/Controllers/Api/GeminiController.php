<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeminiSearchRequest;
use App\Http\Requests\EventsSearchRequest;
use App\Services\GeminiService;
use App\Utils\ApiResponse;
use App\Helpers\LangHelper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    protected GeminiService $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Search using Gemini AI
     * POST /api/gemini/search
     *
     * @param GeminiSearchRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(GeminiSearchRequest $request)
    {
        try {
            $query = $request->input('query');

            $options = [];
            if ($request->has('temperature')) {
                $options['temperature'] = $request->input('temperature');
            }
            if ($request->has('max_output_tokens')) {
                $options['maxOutputTokens'] = $request->input('max_output_tokens');
            }
            if ($request->has('top_p')) {
                $options['topP'] = $request->input('top_p');
            }
            if ($request->has('top_k')) {
                $options['topK'] = $request->input('top_k');
            }

            $result = $this->geminiService->search($query, $options);

            return ApiResponse::success(
                LangHelper::msg('search_success') ?? 'Search completed successfully',
                [
                    'query' => $query,
                    'response' => $result['text'],
                    'model' => $result['model'],
                    'usage' => $result['usage'],
                ]
            );
        } catch (Exception $e) {
            // تسجيل الخطأ بالكامل قبل إرجاع الاستجابة
            Log::error('Gemini Controller Search Error (500)', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error(
                LangHelper::msg('search_error') ?? 'Failed to perform search: ' . $e->getMessage()
            );
        }
    }

    /**
     * Simple search endpoint (alternative)
     * POST /api/gemini/query
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function query(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:10000',
        ]);

        try {
            $query = $request->input('query');
            $result = $this->geminiService->search($query);

            return response()->json([
                'success' => true,
                'data' => [
                    'query' => $query,
                    'response' => $result['text'],
                    'model' => $result['model'],
                ]
            ], 200);
        } catch (Exception $e) {
            // تسجيل الخطأ بالكامل قبل إرجاع الاستجابة
            Log::error('Gemini Controller Query Error (500)', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);

            return ApiResponse::error('Failed to perform search: ' . $e->getMessage());
        }
    }

    /**
     * Search for upcoming events in a country
     * GET /api/gemini/events
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function events(Request $request)
    {
        try {
            // Get language from Accept-Language header (default: ar)
            // Laravel converts headers to lowercase, so check both cases
            $language = $request->header('Accept-Language');

            // If not found, try lowercase version
            if (empty($language)) {
                $language = $request->header('accept-language');
            }

            // Also check 'language' header for backward compatibility
            if (empty($language)) {
                $language = $request->header('language');
            }

            // If still empty, default to Arabic
            if (empty($language)) {
                $language = 'ar';
            }

            // Normalize: convert to lowercase and trim
            $language = strtolower(trim($language));

            // Extract language code if it's in format like "en-US" or "ar-EG"
            if (strpos($language, '-') !== false) {
                $language = explode('-', $language)[0];
            }

            // Validate and default to Arabic if invalid
            if (!in_array($language, ['ar', 'en'])) {
                $language = 'ar';
            }

            // Get country code from Accept-Country header (e.g., EGY, KSA, UAE)
            $countryCode = $request->header('Accept-Country');

            // Also check 'country' header for backward compatibility
            if (empty($countryCode)) {
                $countryCode = $request->header('country');
            }

            // If no country code in header, try query parameter
            if (empty($countryCode)) {
                $countryCode = $request->input('country');
            }

            if (empty($countryCode)) {
                return ApiResponse::badRequest(
                    LangHelper::msg('country_required') ?? 'Country is required. Please provide country code (EGY, KSA, UAE, etc.) in the "Accept-Country" header or query parameter.'
                );
            }

            // Convert country code to country name
            $countryName = $this->getCountryNameFromCode($countryCode);

            if (empty($countryName)) {
                // If code not found, use the provided value as-is (might be a full name)
                $countryName = $countryCode;
            }

            $result = $this->geminiService->searchUpcomingEvents($countryName, $language);

            // Return the JSON response directly as Gemini returns it
            return response()->json($result, 200);
        } catch (Exception $e) {
            // تسجيل الخطأ بالكامل قبل إرجاع الاستجابة
            Log::error('Gemini Controller Events Error (500)', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'country_code_input' => $countryCode,
            ]);

            return ApiResponse::error(
                LangHelper::msg('events_search_error') ?? 'Failed to search for events: ' . $e->getMessage()
            );
        }
    }

    /**
     * Convert country code to country name
     *
     * @param string $code
     * @return string|null
     */
    private function getCountryNameFromCode(string $code): ?string
    {
        // Convert to uppercase for comparison
        $code = strtoupper(trim($code));

        // Mapping of common country codes to English names
        $countryMap = [
            'EGY' => 'Egypt',
            'KSA' => 'Saudi Arabia',
            'SAU' => 'Saudi Arabia',
            'UAE' => 'United Arab Emirates',
            'ARE' => 'United Arab Emirates',
            'JOR' => 'Jordan',
            'KWT' => 'Kuwait',
            'QAT' => 'Qatar',
            'BHR' => 'Bahrain',
            'OMN' => 'Oman',
            'LBN' => 'Lebanon',
            'IRQ' => 'Iraq',
            'SYR' => 'Syria',
            'YEM' => 'Yemen',
            'PSE' => 'Palestine',
            'USA' => 'United States',
            'GBR' => 'United Kingdom',
            'FRA' => 'France',
            'DEU' => 'Germany',
            'ITA' => 'Italy',
            'ESP' => 'Spain',
            'TUR' => 'Turkey',
            'IND' => 'India',
            'CHN' => 'China',
            'JPN' => 'Japan',
            'AUS' => 'Australia',
            'CAN' => 'Canada',
        ];

        // Check if code exists in map
        if (isset($countryMap[$code])) {
            return $countryMap[$code];
        }

        // Try to find in database
        try {
            // افتراض أن هذا الكلاس موجود ويعمل App\Models\Country
            $country = \App\Models\Country::where('code', $code)->first();
            if ($country) {
                return $country->name_en ?? $country->name_ar ?? null;
            }
        } catch (\Exception $e) {
            // إذا فشل الاستعلام، قم بتسجيله ولكن لا تمنع العملية
            Log::warning('Database Country Lookup Failed', ['code' => $code, 'error' => $e->getMessage()]);
        }

        return null;
    }
}
