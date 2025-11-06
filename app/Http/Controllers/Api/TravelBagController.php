<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TravelBagResource;
use App\Http\Resources\BagItemResource;
use App\Http\Requests\UpdateMaxWeightRequest;
use App\Http\Requests\AddItemRequest;
use App\Http\Requests\RemoveItemRequest;
use App\Http\Requests\UpdateItemQuantityRequest;
use App\Services\TravelBagService;
use App\Utils\ApiResponse;
use App\Helpers\LangHelper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TravelBagController extends Controller
{
    public function __construct(
        protected TravelBagService $travelBagService
    ) {}
    /**
     * Get Travel Bag Details
     * GET /api/travel-bag/details?bag_type_id={id}
     * If bag_type_id is not provided, returns all user's bags
     */
    public function details(Request $request)
    {
        try {
            $bagTypeId = $request->query('bag_type_id');
            
            // If bag_type_id is provided and not empty, return single bag
            if ($bagTypeId !== null && $bagTypeId !== '') {
                $travelBag = $this->travelBagService->getOrCreateBagByType((int)$bagTypeId);

                // Get bag type name based on locale
                $locale = app()->getLocale();
                $bagTypeName = $locale === 'ar' 
                    ? ($travelBag->bagType->name_ar ?? '') 
                    : ($travelBag->bagType->name_en ?? '');

                // Create success message with bag name
                $successMessage = str_replace(':bag_name', $bagTypeName, LangHelper::msg('travel_bag_fetched_with_name'));

                return ApiResponse::send(
                    Response::HTTP_OK,
                    $successMessage,
                    new TravelBagResource($travelBag)
                );
            }
            
            // If bag_type_id is not provided, return all user's bags
            $travelBags = $this->travelBagService->getAllUserBags();

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('travel_bags_fetched'),
                TravelBagResource::collection($travelBags)
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('travel_bag_fetch_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Update Maximum Weight
     * PUT /api/travel-bag/max-weight
     */
    public function updateMaxWeight(UpdateMaxWeightRequest $request)
    {
        try {
            $validated = $request->validated();
            $travelBag = $this->travelBagService->updateMaxWeight(
                $validated['max_weight'],
                $validated['weight_unit'] ?? null,
                $validated['bag_type_id'] ?? 1  // Default to main cargo bag (ID = 1)
            );

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('travel_bag_weight_updated'),
                [
                    'max_weight' => round((float) $travelBag->max_weight, 2),
                    'current_weight' => round((float) $travelBag->current_weight, 2),
                    'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('travel_bag_weight_update_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Add Item to Travel Bag
     * POST /api/travel-bag/add-item
     */
    public function addItem(AddItemRequest $request)
    {
        try {
            $validated = $request->validated();
            $result = $this->travelBagService->addItem(
                $validated['item_id'],
                $validated['quantity'] ?? 1,
                $validated['custom_weight'] ?? null,
                $validated['bag_type_id'] ?? 1  // Default to main cargo bag (ID = 1)
            );

            $bagItem = $result['bag_item'];
            $travelBag = $result['travel_bag'];

            // Get bag type name based on locale
            $locale = app()->getLocale();
            $bagTypeName = $locale === 'ar' 
                ? ($travelBag->bagType->name_ar ?? '') 
                : ($travelBag->bagType->name_en ?? '');

            // Create success message with bag name
            $successMessage = str_replace(':bag_name', $bagTypeName, LangHelper::msg('item_added_to_bag_with_name'));

            $weight = $bagItem->custom_weight ?? $bagItem->item->default_weight;
            $totalWeight = $weight * $bagItem->quantity;

            return ApiResponse::send(
                Response::HTTP_OK,
                $successMessage,
                [
                    'item_added' => new BagItemResource($bagItem),
                    'bag_type_id' => $travelBag->bag_type_id,
                    'bag_name' => $bagTypeName,
                    'updated_bag' => [
                        'current_weight' => round((float) $travelBag->current_weight, 2),
                        'max_weight' => round((float) $travelBag->max_weight, 2),
                        'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                        'total_items' => $travelBag->bagItems->sum('quantity'),
                    ]
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('item_add_to_bag_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove Item from Travel Bag
     * DELETE /api/travel-bag/items/{item_id}?bag_type_id={id}&quantity={qty}
     */
    public function removeItem(RemoveItemRequest $request, $itemId)
    {
        try {
            $validated = $request->validated();
            $travelBag = $this->travelBagService->removeItem(
                $itemId,
                $validated['quantity'] ?? null,
                $validated['bag_type_id']
            );

            // Get bag type name based on locale
            $locale = app()->getLocale();
            $bagTypeName = $locale === 'ar' 
                ? ($travelBag->bagType->name_ar ?? '') 
                : ($travelBag->bagType->name_en ?? '');

            // Create success message with bag name
            $successMessage = str_replace(':bag_name', $bagTypeName, LangHelper::msg('item_removed_from_bag_with_name'));

            return ApiResponse::send(
                Response::HTTP_OK,
                $successMessage,
                [
                    'bag_type_id' => $travelBag->bag_type_id,
                    'bag_name' => $bagTypeName,
                    'updated_bag' => [
                        'current_weight' => round((float) $travelBag->current_weight, 2),
                        'max_weight' => round((float) $travelBag->max_weight, 2),
                        'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                        'total_items' => $travelBag->bagItems->sum('quantity'),
                    ]
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('item_remove_from_bag_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Update Item Quantity in Travel Bag
     * PUT /api/travel-bag/items/{item_id}/quantity
     */
    public function updateItemQuantity(UpdateItemQuantityRequest $request, $itemId)
    {
        try {
            $validated = $request->validated();
            $result = $this->travelBagService->updateItemQuantity(
                $itemId,
                $validated['quantity'],
                $validated['bag_type_id']
            );

            $bagItem = $result['bag_item'];
            $travelBag = $result['travel_bag'];

            // Get bag type name based on locale
            $locale = app()->getLocale();
            $bagTypeName = $locale === 'ar' 
                ? ($travelBag->bagType->name_ar ?? '') 
                : ($travelBag->bagType->name_en ?? '');

            // Create success message with bag name
            $successMessage = str_replace(':bag_name', $bagTypeName, LangHelper::msg('item_quantity_updated_with_name'));

            return ApiResponse::send(
                Response::HTTP_OK,
                $successMessage,
                [
                    'item_updated' => new BagItemResource($bagItem),
                    'bag_type_id' => $travelBag->bag_type_id,
                    'bag_name' => $bagTypeName,
                    'updated_bag' => [
                        'current_weight' => round((float) $travelBag->current_weight, 2),
                        'max_weight' => round((float) $travelBag->max_weight, 2),
                        'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                        'total_items' => $travelBag->bagItems->sum('quantity'),
                    ]
                ]
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('item_quantity_update_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get Travel Bag Items
     * GET /api/travel-bag/items?bag_type_id={id}
     */
    public function getItems(Request $request)
    {
        try {
            $bagTypeId = $request->query('bag_type_id', 1); // Default to main cargo bag (ID = 1)
            $travelBag = $this->travelBagService->getBagItems($bagTypeId);

            if (!$travelBag) {
                return ApiResponse::send(
                    Response::HTTP_OK,
                    LangHelper::msg('travel_bag_items_fetched'),
                    [
                        'items' => [],
                        'total_weight' => 0,
                        'total_items' => 0,
                        'max_weight' => 0,
                        'weight_percentage' => 0,
                    ]
                );
            }

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('travel_bag_items_fetched'),
                [
                    'bag_type_id' => $travelBag->bag_type_id,
                    'bag_name' => app()->getLocale() === 'ar'
                        ? ($travelBag->bagType->name_ar ?? '')
                        : ($travelBag->bagType->name_en ?? ''),
                    'items' => BagItemResource::collection($travelBag->bagItems),
                    'total_weight' => round((float) $travelBag->current_weight, 2),
                    'max_weight' => round((float) $travelBag->max_weight, 2),
                    'weight_percentage' => round((float) $travelBag->weight_percentage, 2),
                    'total_items' => $travelBag->bagItems->sum('quantity'),
                ]
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('travel_bag_items_fetch_failed') . ': ' . $e->getMessage());
        }
    }
}
