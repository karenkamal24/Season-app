<?php

namespace App\Http\Controllers\Api\GeographicalGuide;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeographicalGuide\GeographicalCategoryResource;
use App\Models\GeographicalCategory;
use App\Utils\ApiResponse;
use App\Helpers\LangHelper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GeographicalCategoryController extends Controller
{
    /**
     * Get all geographical categories
     */
    public function index(Request $request)
    {
        try {
            $query = GeographicalCategory::where('is_active', true)
                ->with(['subCategories' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('id');

            $categories = $query->get();

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('geographical_categories_fetched') ?? 'Geographical categories fetched successfully',
                GeographicalCategoryResource::collection($categories)
            );
        } catch (\Exception $e) {
            return ApiResponse::send(
                Response::HTTP_BAD_REQUEST,
                $e->getMessage(),
                []
            );
        }
    }

    /**
     * Get single geographical category
     */
    public function show(Request $request, $id)
    {
        try {
            $category = GeographicalCategory::with(['subCategories' => function ($query) {
                $query->where('is_active', true);
            }])->find($id);

            if (!$category) {
                return ApiResponse::send(
                    Response::HTTP_NOT_FOUND,
                    LangHelper::msg('geographical_category_not_found') ?? 'Geographical category not found',
                    []
                );
            }

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('geographical_category_fetched') ?? 'Geographical category fetched successfully',
                new GeographicalCategoryResource($category)
            );
        } catch (\Exception $e) {
            return ApiResponse::send(
                Response::HTTP_BAD_REQUEST,
                $e->getMessage(),
                []
            );
        }
    }
}

