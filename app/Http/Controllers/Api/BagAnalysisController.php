<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyzeBagRequest;
use App\Http\Resources\BagAnalysisResource;
use App\Models\Bag;
use App\Services\BagAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Client\ConnectionException;

class BagAnalysisController extends Controller
{
    protected BagAnalysisService $analysisService;

    public function __construct(BagAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    /**
     * Analyze a bag with AI
     *
     * @param AnalyzeBagRequest $request
     * @param string $bagId
     * @return JsonResponse
     */
    public function analyze(AnalyzeBagRequest $request, string $bagId): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)
                ->with(['items', 'latestAnalysis'])
                ->findOrFail($bagId);

            // Check if bag has items
            if ($bag->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot analyze empty bag. Please add items first.',
                    'message_ar' => 'لا يمكن تحليل حقيبة فارغة. الرجاء إضافة أغراض أولاً.',
                ], 422);
            }

            // Perform analysis
            $analysis = $this->analysisService->analyzeBag($bag, [
                'preferences' => $request->get('preferences', []),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bag analyzed successfully',
                'message_ar' => 'تم تحليل الحقيبة بنجاح',
                'data' => new BagAnalysisResource($analysis),
                'is_fresh_analysis' => true,
            ], 201);

        } catch (ConnectionException $e) {
            // Try to return last analysis if available
            $bag = Bag::where('user_id', Auth::id())
                ->with('latestAnalysis')
                ->find($bagId);

            // Try to get latest analysis from relationship
            if ($bag && $bag->latestAnalysis) {
                return response()->json([
                    'success' => true,
                    'message' => 'Analysis request timed out. Returning last available analysis.',
                    'message_ar' => 'انتهت مهلة طلب التحليل. تم إرجاع آخر تحليل متاح.',
                    'data' => new BagAnalysisResource($bag->latestAnalysis),
                    'is_fresh_analysis' => false,
                    'warning' => 'Connection timeout - showing cached analysis',
                    'warning_ar' => 'انتهت مهلة الاتصال - يتم عرض آخر تحليل محفوظ',
                ], 200);
            }

            // If latestAnalysis relationship didn't work, try direct query
            if ($bag) {
                $lastAnalysis = $bag->analyses()
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($lastAnalysis) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Analysis request timed out. Returning last available analysis.',
                        'message_ar' => 'انتهت مهلة طلب التحليل. تم إرجاع آخر تحليل متاح.',
                        'data' => new BagAnalysisResource($lastAnalysis),
                        'is_fresh_analysis' => false,
                        'warning' => 'Connection timeout - showing cached analysis',
                        'warning_ar' => 'انتهت مهلة الاتصال - يتم عرض آخر تحليل محفوظ',
                    ], 200);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Analysis request timed out and no previous analysis found. Please try again.',
                'message_ar' => 'انتهت مهلة طلب التحليل ولا يوجد تحليل سابق. يرجى المحاولة مرة أخرى.',
                'error' => 'Connection timeout',
            ], 504);
        } catch (\Exception $e) {
            // Try to return last analysis if available
            $bag = Bag::where('user_id', Auth::id())
                ->with('latestAnalysis')
                ->find($bagId);

            // Check if error is related to quota/API key
            $errorMessage = $e->getMessage();
            $isQuotaError = stripos($errorMessage, 'quota') !== false || 
                           stripos($errorMessage, 'exceeded') !== false ||
                           stripos($errorMessage, 'billing') !== false;

            if ($bag && $bag->latestAnalysis) {
                $warningMessage = $isQuotaError 
                    ? 'API quota exceeded - showing cached analysis'
                    : 'Analysis error - showing cached analysis';
                $warningMessageAr = $isQuotaError
                    ? 'تم تجاوز الحصة المسموحة - يتم عرض آخر تحليل محفوظ'
                    : 'خطأ في التحليل - يتم عرض آخر تحليل محفوظ';

                return response()->json([
                    'success' => true,
                    'message' => 'Analysis service unavailable. Returning last available analysis.',
                    'message_ar' => 'خدمة التحليل غير متاحة حالياً. تم إرجاع آخر تحليل متاح.',
                    'data' => new BagAnalysisResource($bag->latestAnalysis),
                    'is_fresh_analysis' => false,
                    'warning' => $warningMessage,
                    'warning_ar' => $warningMessageAr,
                ], 200);
            }

            // No previous analysis found
            $errorResponse = [
                'success' => false,
                'error' => $errorMessage,
            ];

            if ($isQuotaError) {
                $errorResponse['message'] = 'Analysis service quota exceeded and no previous analysis found. Please try again later.';
                $errorResponse['message_ar'] = 'تم تجاوز الحصة المسموحة لخدمة التحليل ولا يوجد تحليل سابق. يرجى المحاولة لاحقاً.';
            } else {
                $errorResponse['message'] = 'Failed to analyze bag and no previous analysis found.';
                $errorResponse['message_ar'] = 'فشل في تحليل الحقيبة ولا يوجد تحليل سابق.';
            }

            return response()->json($errorResponse, 500);
        }
    }

    /**
     * Get bag analyses history
     *
     * @param string $bagId
     * @return JsonResponse
     */
    public function history(string $bagId): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)->findOrFail($bagId);

            $analyses = $bag->analyses()
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Analysis history retrieved successfully',
                'message_ar' => 'تم جلب سجل التحليلات بنجاح',
                'data' => BagAnalysisResource::collection($analyses),
                'pagination' => [
                    'total' => $analyses->total(),
                    'per_page' => $analyses->perPage(),
                    'current_page' => $analyses->currentPage(),
                    'last_page' => $analyses->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analysis history',
                'message_ar' => 'فشل في جلب سجل التحليلات',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get latest analysis for a bag
     *
     * @param string $bagId
     * @return JsonResponse
     */
    public function latest(string $bagId): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)
                ->with('latestAnalysis')
                ->findOrFail($bagId);

            if (!$bag->latestAnalysis) {
                return response()->json([
                    'success' => false,
                    'message' => 'No analysis found for this bag',
                    'message_ar' => 'لا يوجد تحليل لهذه الحقيبة',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Latest analysis retrieved successfully',
                'message_ar' => 'تم جلب آخر تحليل بنجاح',
                'data' => new BagAnalysisResource($bag->latestAnalysis),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analysis',
                'message_ar' => 'فشل في جلب التحليل',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get smart alert for a bag
     *
     * @param string $bagId
     * @return JsonResponse
     */
    public function smartAlert(string $bagId): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)
                ->with('items')
                ->findOrFail($bagId);

            $alert = $this->analysisService->generateSmartAlert($bag);

            if (!$alert) {
                return response()->json([
                    'success' => true,
                    'message' => 'No alerts for this bag',
                    'message_ar' => 'لا توجد تنبيهات لهذه الحقيبة',
                    'data' => null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Smart alert retrieved successfully',
                'message_ar' => 'تم جلب التنبيه الذكي بنجاح',
                'data' => $alert,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate smart alert',
                'message_ar' => 'فشل في توليد التنبيه الذكي',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
