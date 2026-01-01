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
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BagController extends Controller
{
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
            ->with(['items', 'latestAnalysis'])
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

        // Paginate
        $perPage = $request->get('per_page', 15);
        $bags = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Bags retrieved successfully',
            'message_ar' => 'تم جلب الحقائب بنجاح',
            'data' => BagResource::collection($bags),
            'pagination' => [
                'total' => $bags->total(),
                'per_page' => $bags->perPage(),
                'current_page' => $bags->currentPage(),
                'last_page' => $bags->lastPage(),
            ],
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
                        'category' => $itemData['category'],
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
                'message' => 'Bag created successfully',
                'message_ar' => 'تم إنشاء الحقيبة بنجاح',
                'data' => new BagResource($bag->load(['items', 'latestAnalysis'])),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create bag',
                'message_ar' => 'فشل في إنشاء الحقيبة',
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
            ->with(['items', 'latestAnalysis'])
            ->withCount('items')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Bag retrieved successfully',
            'message_ar' => 'تم جلب الحقيبة بنجاح',
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
                'message' => 'Bag updated successfully',
                'message_ar' => 'تم تحديث الحقيبة بنجاح',
                'data' => new BagResource($bag->load(['items', 'latestAnalysis'])),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bag',
                'message_ar' => 'فشل في تحديث الحقيبة',
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
                'message' => 'Bag deleted successfully',
                'message_ar' => 'تم حذف الحقيبة بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bag',
                'message_ar' => 'فشل في حذف الحقيبة',
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

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully',
                'message_ar' => 'تم إضافة الغرض بنجاح',
                'data' => new SmartBagItemResource($item),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item',
                'message_ar' => 'فشل في إضافة الغرض',
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

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'message_ar' => 'تم تحديث الغرض بنجاح',
                'data' => new SmartBagItemResource($item),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item',
                'message_ar' => 'فشل في تحديث الغرض',
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
                'message' => 'Item deleted successfully',
                'message_ar' => 'تم حذف الغرض بنجاح',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item',
                'message_ar' => 'فشل في حذف الغرض',
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

            return response()->json([
                'success' => true,
                'message' => 'Item packed status updated',
                'message_ar' => 'تم تحديث حالة التحزيم',
                'data' => new SmartBagItemResource($item),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle packed status',
                'message_ar' => 'فشل في تغيير حالة التحزيم',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
