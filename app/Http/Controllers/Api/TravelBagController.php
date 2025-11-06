<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TravelBagResource;
use App\Http\Resources\BagItemResource;
use App\Http\Requests\UpdateMaxWeightRequest;
use App\Http\Requests\AddItemRequest;
use App\Http\Requests\RemoveItemRequest;
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
     * GET /api/travel-bag/details
     */
    public function details(Request $request)
    {
        try {
            $travelBag = $this->travelBagService->getOrCreateMainBag();

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('travel_bag_fetched'),
                new TravelBagResource($travelBag)
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
                $validated['weight_unit'] ?? null
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
                $validated['custom_weight'] ?? null
            );

            $bagItem = $result['bag_item'];
            $travelBag = $result['travel_bag'];

            $weight = $bagItem->custom_weight ?? $bagItem->item->default_weight;
            $totalWeight = $weight * $bagItem->quantity;

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('item_added_to_bag'),
                [
                    'item_added' => new BagItemResource($bagItem),
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
     * DELETE /api/travel-bag/items/{item_id}
     */
    public function removeItem(RemoveItemRequest $request, $itemId)
    {
        try {
            $validated = $request->validated();
            $travelBag = $this->travelBagService->removeItem(
                $itemId,
                $validated['quantity'] ?? null
            );

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('item_removed_from_bag'),
                [
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
     * Get Travel Bag Items
     * GET /api/travel-bag/items
     */
    public function getItems(Request $request)
    {
        try {
            $travelBag = $this->travelBagService->getBagItems();

            if (!$travelBag) {
                return ApiResponse::send(
                    Response::HTTP_OK,
                    LangHelper::msg('travel_bag_items_fetched'),
                    [
                        'items' => [],
                        'total_weight' => 0,
                        'total_items' => 0,
                    ]
                );
            }

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('travel_bag_items_fetched'),
                [
                    'items' => BagItemResource::collection($travelBag->bagItems),
                    'total_weight' => round((float) $travelBag->current_weight, 2),
                    'total_items' => $travelBag->bagItems->sum('quantity'),
                ]
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('travel_bag_items_fetch_failed') . ': ' . $e->getMessage());
        }
    }
}
