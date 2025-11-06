<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BagTypeResource;
use App\Services\BagTypeService;
use App\Utils\ApiResponse;
use App\Helpers\LangHelper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BagTypeController extends Controller
{
    public function __construct(
        protected BagTypeService $bagTypeService
    ) {}

    /**
     * Get All Bag Types
     * GET /api/bag-types
     */
    public function index(Request $request)
    {
        try {
            $bagTypes = $this->bagTypeService->getAllBagTypes();

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('bag_types_fetched'),
                BagTypeResource::collection($bagTypes)
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('bag_types_fetch_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get Single Bag Type Details
     * GET /api/bag-types/{id}
     */
    public function show($id)
    {
        try {
            $bagType = $this->bagTypeService->getBagTypeById($id);

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('bag_type_fetched'),
                new BagTypeResource($bagType)
            );
        } catch (NotFoundHttpException $e) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                $e->getMessage()
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('bag_type_fetch_failed') . ': ' . $e->getMessage());
        }
    }
}

