<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    /**
     * Get all active item categories
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $lang = $request->header('Accept-Language', 'ar');

        $categories = ItemCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name_en')
            ->get()
            ->map(function ($category) use ($lang) {
                return [
                    'id' => $category->id,
                    'name' => $lang === 'ar' ? $category->name_ar : $category->name_en,
                    'name_ar' => $category->name_ar,
                    'name_en' => $category->name_en,
                    'icon' => $category->icon,
                    'icon_color' => $category->icon_color,
                    'sort_order' => $category->sort_order,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Item categories retrieved successfully',
            'message_ar' => 'تم جلب فئات الأغراض بنجاح',
            'data' => $categories,
        ]);
    }

    /**
     * Get single category
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $lang = $request->header('Accept-Language', 'ar');

        $category = ItemCategory::where('is_active', true)->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Item category retrieved successfully',
            'message_ar' => 'تم جلب الفئة بنجاح',
            'data' => [
                'id' => $category->id,
                'name' => $lang === 'ar' ? $category->name_ar : $category->name_en,
                'name_ar' => $category->name_ar,
                'name_en' => $category->name_en,
                'icon' => $category->icon,
                'icon_color' => $category->icon_color,
                'sort_order' => $category->sort_order,
            ],
        ]);
    }
}
