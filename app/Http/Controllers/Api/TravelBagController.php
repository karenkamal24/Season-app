<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TravelBagResource;
use App\Http\Resources\BagItemResource;
use App\Models\TravelBag;
use App\Models\BagItem;
use App\Models\Item;
use App\Models\BagType;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TravelBagController extends Controller
{
    /**
     * Get Travel Bag Details
     * GET /api/travel-bag/details
     */
    public function details(Request $request)
    {
        try {
            $user = Auth::user();

            // Get or create main travel bag
            $bagType = BagType::where('code', 'main_cargo')->first();
            if (!$bagType) {
                return ApiResponse::badRequest('Bag type not found');
            }

            $travelBag = TravelBag::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'bag_type_id' => $bagType->id,
                ],
                [
                    'max_weight' => 23.0,
                    'weight_unit' => 'kg',
                    'is_active' => true,
                ]
            );

            $travelBag->load(['bagItems.item.category', 'bagType']);

            return response()->json([
                'success' => true,
                'data' => new TravelBagResource($travelBag)
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve travel bag details: ' . $e->getMessage());
        }
    }

    /**
     * Update Maximum Weight
     * PUT /api/travel-bag/max-weight
     */
    public function updateMaxWeight(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'max_weight' => 'required|numeric|min:0',
                'weight_unit' => 'nullable|string|in:kg,lb',
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

            $user = Auth::user();
            $bagType = BagType::where('code', 'main_cargo')->first();

            if (!$bagType) {
                return ApiResponse::badRequest('Bag type not found');
            }

            $travelBag = TravelBag::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'bag_type_id' => $bagType->id,
                ],
                [
                    'max_weight' => 23.0,
                    'weight_unit' => 'kg',
                    'is_active' => true,
                ]
            );

            $travelBag->update([
                'max_weight' => $request->max_weight,
                'weight_unit' => $request->weight_unit ?? $travelBag->weight_unit,
            ]);

            $travelBag->load('bagItems.item');

            return response()->json([
                'success' => true,
                'message' => 'Maximum weight updated successfully',
                'data' => [
                    'max_weight' => round((float) $travelBag->max_weight, 2),
                    'current_weight' => round((float) $travelBag->current_weight, 2),
                    'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                ]
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to update maximum weight: ' . $e->getMessage());
        }
    }

    /**
     * Add Item to Travel Bag
     * POST /api/travel-bag/add-item
     */
    public function addItem(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|exists:items,id',
                'quantity' => 'nullable|integer|min:1',
                'custom_weight' => 'nullable|numeric|min:0',
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

            $user = Auth::user();
            $bagType = BagType::where('code', 'main_cargo')->first();

            if (!$bagType) {
                return ApiResponse::badRequest('Bag type not found');
            }

            $travelBag = TravelBag::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'bag_type_id' => $bagType->id,
                ],
                [
                    'max_weight' => 23.0,
                    'weight_unit' => 'kg',
                    'is_active' => true,
                ]
            );

            $item = Item::findOrFail($request->item_id);
            $quantity = $request->quantity ?? 1;

            // Check if item already exists in bag
            $bagItem = BagItem::where('travel_bag_id', $travelBag->id)
                ->where('item_id', $item->id)
                ->first();

            if ($bagItem) {
                // Update quantity
                $bagItem->quantity += $quantity;
                if ($request->has('custom_weight')) {
                    $bagItem->custom_weight = $request->custom_weight;
                }
                $bagItem->save();
            } else {
                // Create new bag item
                $bagItem = BagItem::create([
                    'travel_bag_id' => $travelBag->id,
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                    'custom_weight' => $request->custom_weight,
                ]);
            }

            $bagItem->load('item.category');
            $travelBag->load('bagItems.item');

            $weight = $bagItem->custom_weight ?? $bagItem->item->default_weight;
            $totalWeight = $weight * $bagItem->quantity;

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data' => [
                    'item_added' => [
                        'item_id' => $bagItem->item_id,
                        'name' => $bagItem->item->name_en,
                        'name_arabic' => $bagItem->item->name_ar,
                        'category' => $bagItem->item->category->name_en ?? null,
                        'quantity' => $bagItem->quantity,
                        'weight_per_item' => round($weight, 2),
                        'total_weight' => round($totalWeight, 2),
                    ],
                    'updated_bag' => [
                        'current_weight' => round((float) $travelBag->current_weight, 2),
                        'max_weight' => round((float) $travelBag->max_weight, 2),
                        'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                        'total_items' => $travelBag->bagItems->sum('quantity'),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to add item: ' . $e->getMessage());
        }
    }

    /**
     * Remove Item from Travel Bag
     * DELETE /api/travel-bag/items/{item_id}
     */
    public function removeItem(Request $request, $itemId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'nullable|integer|min:1',
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

            $user = Auth::user();
            $bagType = BagType::where('code', 'main_cargo')->first();

            if (!$bagType) {
                return ApiResponse::badRequest('Bag type not found');
            }

            $travelBag = TravelBag::where('user_id', $user->id)
                ->where('bag_type_id', $bagType->id)
                ->first();

            if (!$travelBag) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Travel bag not found'
                    ]
                ], 404);
            }

            $bagItem = BagItem::where('travel_bag_id', $travelBag->id)
                ->where('item_id', $itemId)
                ->first();

            if (!$bagItem) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'NOT_FOUND',
                        'message' => 'Item not found in bag'
                    ]
                ], 404);
            }

            $quantityToRemove = $request->quantity ?? $bagItem->quantity;

            if ($quantityToRemove >= $bagItem->quantity) {
                // Remove entire item
                $bagItem->delete();
            } else {
                // Decrease quantity
                $bagItem->quantity -= $quantityToRemove;
                $bagItem->save();
            }

            $travelBag->load('bagItems.item');

            return response()->json([
                'success' => true,
                'message' => 'Item removed successfully',
                'data' => [
                    'updated_bag' => [
                        'current_weight' => round((float) $travelBag->current_weight, 2),
                        'max_weight' => round((float) $travelBag->max_weight, 2),
                        'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                        'total_items' => $travelBag->bagItems->sum('quantity'),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to remove item: ' . $e->getMessage());
        }
    }

    /**
     * Get Travel Bag Items
     * GET /api/travel-bag/items
     */
    public function getItems(Request $request)
    {
        try {
            $user = Auth::user();
            $bagType = BagType::where('code', 'main_cargo')->first();

            if (!$bagType) {
                return ApiResponse::badRequest('Bag type not found');
            }

            $travelBag = TravelBag::where('user_id', $user->id)
                ->where('bag_type_id', $bagType->id)
                ->first();

            if (!$travelBag) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'items' => [],
                        'total_weight' => 0,
                        'total_items' => 0,
                    ]
                ]);
            }

            $travelBag->load('bagItems.item.category');

            $items = $travelBag->bagItems->map(function ($bagItem) {
                $weight = $bagItem->custom_weight ?? $bagItem->item->default_weight;
                $totalWeight = $weight * $bagItem->quantity;

                return [
                    'item_id' => $bagItem->item_id,
                    'name' => $bagItem->item->name_en,
                    'name_arabic' => $bagItem->item->name_ar,
                    'category' => $bagItem->item->category->name_en ?? null,
                    'category_arabic' => $bagItem->item->category->name_ar ?? null,
                    'quantity' => $bagItem->quantity,
                    'weight_per_item' => round($weight, 2),
                    'total_weight' => round($totalWeight, 2),
                    'icon' => $bagItem->item->icon,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $items,
                    'total_weight' => round((float) $travelBag->current_weight, 2),
                    'total_items' => $travelBag->bagItems->sum('quantity'),
                ]
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to retrieve items: ' . $e->getMessage());
        }
    }
}
