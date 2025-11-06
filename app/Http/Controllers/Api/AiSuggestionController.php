<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\TravelBag;
use App\Models\BagType;
use App\Models\BagItem;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AiSuggestionController extends Controller
{
    /**
     * Get AI Suggestions
     * GET /api/ai/suggestions
     */
    public function suggestions(Request $request)
    {
        try {
            $user = Auth::user();

            // Get items already in bag
            // Main cargo bag is always ID 1 (first bag type created)
            $bagType = BagType::find(1);
            $travelBag = null;
            $itemsInBag = [];

            if ($bagType) {
                $travelBag = TravelBag::where('user_id', $user->id)
                    ->where('bag_type_id', $bagType->id)
                    ->with('bagItems.item')
                    ->first();

                if ($travelBag) {
                    $itemsInBag = $travelBag->bagItems->pluck('item_id')->toArray();
                }
            }

            // Get destination info (basic implementation)
            $destination = [
                'name' => $request->input('destination', 'Unknown'),
                'name_arabic' => $request->input('destination', 'غير محدد'),
            ];

            // Basic AI suggestion logic - suggest essential items
            // This can be enhanced with actual AI/ML logic based on destination
            $essentialCategories = ItemCategory::whereIn('name_en', [
                'Electronics',
                'Personal Essentials',
                'First Aids'
            ])->pluck('id')->toArray();

            $query = Item::whereIn('category_id', $essentialCategories)
                ->where('is_active', true)
                ->with('category')
                ->orderBy('sort_order')
                ->limit(10);

            // Exclude items already in bag if requested
            $includeCurrentItems = $request->input('include_current_items', true);
            if (!$includeCurrentItems && !empty($itemsInBag)) {
                $query->whereNotIn('id', $itemsInBag);
            }

            $items = $query->get();

            $suggestions = $items->map(function ($item) use ($itemsInBag, $destination) {
                $weight = $item->default_weight;
                $priority = 'medium';

                // Set priority based on item importance
                if (in_array($item->name_en, ['Passport / Visa / ID', 'Medicines', 'Cash', 'Credit Card'])) {
                    $priority = 'high';
                }

                return [
                    'item_id' => $item->id,
                    'name' => $item->name_en,
                    'name_arabic' => $item->name_ar,
                    'weight' => round($weight, 2),
                    'weight_unit' => $item->weight_unit ?? 'kg',
                    'category' => $item->category->name_en ?? null,
                    'category_arabic' => $item->category->name_ar ?? null,
                    'description' => $item->description_en ?? 'Essential travel item',
                    'description_arabic' => $item->description_ar ?? 'عنصر أساسي للسفر',
                    'reason' => 'Recommended for ' . $destination['name'],
                    'priority' => $priority,
                    'icon' => $item->icon,
                    'is_in_bag' => in_array($item->id, $itemsInBag),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'destination' => $destination,
                    'suggestions' => $suggestions,
                    'total_suggestions' => $suggestions->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to get AI suggestions: ' . $e->getMessage());
        }
    }

    /**
     * Add Suggested Item to Bag
     * POST /api/ai/suggestions/add-item
     */
    public function addSuggestedItem(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|exists:items,id',
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
            // Main cargo bag is always ID 1 (first bag type created)
            $bagType = BagType::find(1);

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
                $bagItem->quantity += $quantity;
                $bagItem->save();
            } else {
                $bagItem = BagItem::create([
                    'travel_bag_id' => $travelBag->id,
                    'item_id' => $item->id,
                    'quantity' => $quantity,
                ]);
            }

            $travelBag->load('bagItems.item');
            $weight = $bagItem->custom_weight ?? $bagItem->item->default_weight;

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'data' => [
                    'item_added' => [
                        'item_id' => $bagItem->item_id,
                        'name_arabic' => $bagItem->item->name_ar,
                        'quantity' => $bagItem->quantity,
                        'weight' => round($weight, 2),
                    ],
                    'updated_bag' => [
                        'current_weight' => round((float) $travelBag->current_weight, 2),
                        'max_weight' => round((float) $travelBag->max_weight, 2),
                        'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to add item: ' . $e->getMessage());
        }
    }
}

