<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnalyzeBagRequest;
use App\Http\Resources\BagAnalysisResource;
use App\Models\Bag;
use App\Services\BagAnalysisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
                ->with('items')
                ->findOrFail($bagId);

            // Check if bag has items
            if ($bag->items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot analyze empty bag. Please add items first.',
                    'message_ar' => 'لا يمكن تحليل حقيبة فارغة. الرجاء إضافة أغراض أولاً.',
                ], 422);
            }

            // Check if already analyzed recently (unless force reanalysis)
            if ($bag->is_analyzed && 
                $bag->last_analyzed_at && 
                $bag->last_analyzed_at->diffInHours(now()) < 24 &&
                !$request->boolean('force_reanalysis')
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bag was analyzed recently. Use force_reanalysis=true to reanalyze.',
                    'message_ar' => 'تم تحليل الحقيبة مؤخراً. استخدم force_reanalysis=true لإعادة التحليل.',
                    'last_analyzed_at' => $bag->last_analyzed_at->toIso8601String(),
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
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to analyze bag',
                'message_ar' => 'فشل في تحليل الحقيبة',
                'error' => $e->getMessage(),
            ], 500);
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
