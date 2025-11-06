<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Http\Resources\ItemCategoryResource;
use App\Http\Requests\GetItemsRequest;
use App\Services\ItemService;
use App\Utils\ApiResponse;
use App\Helpers\LangHelper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ItemController extends Controller
{
    public function __construct(
        protected ItemService $itemService
    ) {}

    /**
     * Get All Categories
     * GET /api/items/categories
     */
    public function categories(Request $request)
    {
        try {
            $categories = $this->itemService->getAllCategories();

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('items_categories_fetched'),
                ItemCategoryResource::collection($categories)
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('items_categories_fetch_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get Items by Category
     * GET /api/items?category_id={category_id}
     */
    public function index(GetItemsRequest $request)
    {
        try {
            $validated = $request->validated();
            $items = $this->itemService->getItemsByCategory($validated['category_id']);

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('items_fetched'),
                ItemResource::collection($items)
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('items_fetch_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get Single Item Details
     * GET /api/items/{item_id}
     */
    public function show($id)
    {
        try {
            $item = $this->itemService->getItemById($id);

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('item_fetched'),
                new ItemResource($item)
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('item_fetch_failed') . ': ' . $e->getMessage());
        }
    }
}
