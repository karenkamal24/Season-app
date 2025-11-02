<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackingTipResource;
use App\Models\PackingTip;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;

class PackingTipController extends Controller
{
    /**
     * Get Packing Tips
     * GET /api/packing-tips
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);

            $tips = PackingTip::where('is_active', true)
                ->orderBy('priority')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'tips' => PackingTipResource::collection($tips),
                    'total_tips' => $tips->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve packing tips: ' . $e->getMessage());
        }
    }
}
