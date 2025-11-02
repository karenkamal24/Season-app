<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Http\Resources\ItemCategoryResource;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    /**
     * Get All Categories
     * GET /api/items/categories
     */
    public function categories(Request $request)
    {
        try {
            $categories = ItemCategory::where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => ItemCategoryResource::collection($categories)
                ]
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve categories: ' . $e->getMessage());
        }
    }

    /**
     * Get Items by Category
     * GET /api/items?category_id={category_id}
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:item_categories,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'VALIDATION_ERROR',
                        'message' => 'Validation failed',
                        'details' => collect($validator->errors()->all())->map(function ($message) {
                            return ['message' => $message];
                        })->values()
                    ]
                ], 400);
            }

            $category = ItemCategory::findOrFail($request->category_id);
            $items = Item::where('category_id', $category->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->with('category')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => [
                        'category_id' => $category->id,
                        'name' => $category->name_en,
                        'name_arabic' => $category->name_ar,
                    ],
                    'items' => ItemResource::collection($items)
                ]
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve items: ' . $e->getMessage());
        }
    }

    /**
     * Get Single Item Details
     * GET /api/items/{item_id}
     */
    public function show($id)
    {
        try {
            $item = Item::with('category')
                ->where('is_active', true)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new ItemResource($item)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Item not found'
                ]
            ], 404);
        }
    }
}
