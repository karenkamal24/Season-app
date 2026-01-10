<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBagRequest;
use App\Http\Requests\UpdateBagRequest;
use App\Http\Requests\StoreBagItemRequest;
use App\Http\Resources\BagResource;
use App\Http\Resources\SmartBagItemResource;
use App\Models\Bag;
use App\Models\BagItem;
use App\Services\GeminiAIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\LangHelper;

class BagController extends Controller
{
    protected GeminiAIService $geminiService;

    public function __construct(GeminiAIService $geminiService)
    {
        $this->geminiService = $geminiService;
    }
    /**
     * Display a listing of the user's bags.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $query = Bag::where('user_id', $user->id)
            ->with(['items.itemCategory', 'latestAnalysis'])
            ->withCount('items');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by trip_type
        if ($request->has('trip_type')) {
            $query->where('trip_type', $request->trip_type);
        }

        // Filter upcoming trips
        if ($request->boolean('upcoming')) {
            $query->upcoming();
        }

        // Sort
        $sortBy = $request->get('sort_by', 'departure_date');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Get all bags
        $bags = $query->get();

        return response()->json([
            'success' => true,
            'message' => LangHelper::msg('bags_retrieved'),
            'data' => BagResource::collection($bags),
        ]);
    }

    /**
     * Store a newly created bag in storage.
     *
     * @param StoreBagRequest $request
     * @return JsonResponse
     */
    public function store(StoreBagRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Create bag
            $bag = Bag::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'trip_type' => $request->trip_type,
                'duration' => $request->duration,
                'destination' => $request->destination,
                'departure_date' => $request->departure_date,
                'max_weight' => $request->max_weight ?? 20.00,
                'status' => $request->status ?? 'draft',
                'preferences' => $request->preferences ?? [],
            ]);

            // Create items if provided
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $itemData) {
                    $bag->items()->create([
                        'name' => $itemData['name'],
                        'weight' => $itemData['weight'],
                        'item_category_id' => $itemData['item_category_id'],
                        'essential' => $itemData['essential'] ?? false,
                        'packed' => $itemData['packed'] ?? false,
                        'notes' => $itemData['notes'] ?? null,
                        'quantity' => $itemData['quantity'] ?? 1,
                    ]);
                }
            }

            // Recalculate total weight
            $bag->recalculateWeight();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('bag_created'),
                'data' => new BagResource($bag->load(['items.itemCategory', 'latestAnalysis'])),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('bag_create_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified bag.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();

        $bag = Bag::where('user_id', $user->id)
            ->with(['items.itemCategory', 'latestAnalysis'])
            ->withCount('items')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => LangHelper::msg('bag_retrieved'),
            'data' => new BagResource($bag),
        ]);
    }

    /**
     * Update the specified bag in storage.
     *
     * @param UpdateBagRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateBagRequest $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)->findOrFail($id);

            $bag->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('bag_updated'),
                'data' => new BagResource($bag->load(['items.itemCategory', 'latestAnalysis'])),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('bag_update_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified bag from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)->findOrFail($id);
            $bag->delete();

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('bag_deleted'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('bag_delete_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add item to bag
     *
     * @param StoreBagItemRequest $request
     * @param string $bagId
     * @return JsonResponse
     */
    public function addItem(StoreBagItemRequest $request, string $bagId): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)->findOrFail($bagId);

            $item = $bag->items()->create($request->validated());
            $item->load('itemCategory');

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('item_added'),
                'data' => new SmartBagItemResource($item),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('item_add_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update bag item
     *
     * @param StoreBagItemRequest $request
     * @param string $bagId
     * @param string $itemId
     * @return JsonResponse
     */
    public function updateItem(StoreBagItemRequest $request, string $bagId, string $itemId): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)->findOrFail($bagId);
            $item = $bag->items()->findOrFail($itemId);

            $item->update($request->validated());
            $item->load('itemCategory');

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('item_updated'),
                'data' => new SmartBagItemResource($item),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('item_update_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete bag item
     *
     * @param string $bagId
     * @param string $itemId
     * @return JsonResponse
     */
    public function deleteItem(string $bagId, string $itemId): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)->findOrFail($bagId);
            $item = $bag->items()->findOrFail($itemId);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('item_deleted'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('item_delete_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle item packed status
     *
     * @param string $bagId
     * @param string $itemId
     * @return JsonResponse
     */
    public function toggleItemPacked(string $bagId, string $itemId): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)->findOrFail($bagId);
            $item = $bag->items()->findOrFail($itemId);

            $item->togglePacked();
            $item->load('itemCategory');

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('item_packed_updated'),
                'data' => new SmartBagItemResource($item),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('item_packed_toggle_failed'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get AI-generated packing categories
     * GET /api/smart-bags/ai/categories
     * Uses Accept-Language header (ar/en)
     */
    public function getAICategories(Request $request): JsonResponse
    {
        try {
            // Get language from query parameter (if provided), otherwise from Accept-Language header
            // Use same method as ItemCategoryController
            $language = $request->query('language');

            if (!$language) {
                // Get from Accept-Language header (same as ItemCategoryController)
                $headerLang = $request->header('Accept-Language', 'ar');
                $language = strtolower(explode('-', trim($headerLang))[0]);
            }

            // Validate language, default to 'ar' if not supported
            if (!in_array($language, ['ar', 'en'])) {
                $language = 'ar';
            }

            // Get AI-generated categories
            $categories = $this->geminiService->generatePackingCategories($language);

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('ai_categories_generated') ?? 'AI categories generated successfully',
                'data' => [
                    'categories' => $categories,
                    'language' => $language,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => (LangHelper::msg('categories_retrieval_failed') ?? 'Failed to retrieve categories') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get AI-suggested items for a category
     * GET /api/smart-bags/ai/suggest-items?category={category_name}
     * Uses Accept-Language header (ar/en)
     */
    public function getAISuggestedItems(Request $request): JsonResponse
    {
        try {
            $categoryName = $request->query('category');

            if (!$categoryName) {
                return response()->json([
                    'success' => false,
                    'message' => LangHelper::msg('category_required') ?? 'Category parameter is required',
                ], 400);
            }

            // Get language from query parameter (if provided), otherwise from Accept-Language header
            $language = $request->query('language');

            if (!$language) {
                // Get from Accept-Language header
                $headerLang = $request->header('Accept-Language', 'ar');
                $language = strtolower(explode('-', trim($headerLang))[0]);
            }

            // Validate language, default to 'ar' if not supported
            if (!in_array($language, ['ar', 'en'])) {
                $language = 'ar';
            }

            // Get AI suggestions for this category
            $items = $this->geminiService->suggestItemsForCategory($categoryName, $language);

            // Convert weight from grams to kilograms for consistency with the app
            $items = array_map(function ($item) {
                if (isset($item['weight']) && is_numeric($item['weight'])) {
                    // Convert grams to kilograms (divide by 1000)
                    $item['weight'] = round($item['weight'] / 1000, 3);
                    $item['weight_grams'] = (int)($item['weight'] * 1000); // Keep original in grams for reference
                }
                return $item;
            }, $items);

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('ai_items_suggested') ?? 'AI items suggested successfully',
                'data' => [
                    'category' => $categoryName,
                    'items' => $items,
                    'language' => $language,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => (LangHelper::msg('ai_items_suggestion_failed') ?? 'Failed to suggest AI items') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add AI-suggested item to bag
     * POST /api/smart-bags/{bagId}/ai/add-item
     * body: {
     *   "item_name": "string",
     *   "weight": float (in kg),
     *   "essential": boolean,
     *   "quantity": int (optional, default: 1)
     * }
     */
    public function addAIItem(Request $request, string $bagId): JsonResponse
    {
        try {
            $user = Auth::user();

            $bag = Bag::where('user_id', $user->id)->findOrFail($bagId);

            $validated = $request->validate([
                'item_name' => ['required', 'string', 'max:255'],
                'weight' => ['required', 'numeric', 'min:0', 'max:999.99'],
                'essential' => ['nullable', 'boolean'],
                'quantity' => ['nullable', 'integer', 'min:1'],
            ]);

            // Check if adding this item will exceed max weight
            $currentWeight = (float) ($bag->total_weight ?? 0);
            $itemWeight = (float) $validated['weight'];
            $quantity = (int) ($validated['quantity'] ?? 1);
            $newWeight = $currentWeight + ($itemWeight * $quantity);

            if ($newWeight > $bag->max_weight) {
                return response()->json([
                    'success' => false,
                    'message' => LangHelper::msg('cannot_add_more_weight_exceeded') ?? 'Cannot add more items. Weight limit exceeded.',
                ], 400);
            }

            // Create bag item (no category needed - all from AI)
            $itemData = [
                'name' => $validated['item_name'],
                'weight' => $validated['weight'],
                'essential' => $validated['essential'] ?? false,
                'quantity' => $validated['quantity'] ?? 1,
            ];

            $item = $bag->items()->create($itemData);
            $item->load('itemCategory');

            // Recalculate bag weight
            $bag->recalculateWeight();
            $bag->load(['items.itemCategory', 'latestAnalysis']);

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('ai_item_added') ?? 'AI item added successfully',
                'data' => [
                    'item' => new SmartBagItemResource($item),
                    'bag' => [
                        'current_weight' => round((float) $bag->total_weight, 2),
                        'max_weight' => round((float) $bag->max_weight, 2),
                        'weight_percentage' => round((float) $bag->weight_percentage, 2),
                    ],
                ],
            ], 201);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('bag_not_found') ?? 'Bag not found',
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('validation_failed') ?? 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => (LangHelper::msg('ai_item_add_failed') ?? 'Failed to add AI item') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}
