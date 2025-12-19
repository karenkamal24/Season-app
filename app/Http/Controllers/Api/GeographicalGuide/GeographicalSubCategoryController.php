<?php

namespace App\Http\Controllers\Api\GeographicalGuide;

use App\Http\Controllers\Controller;
use App\Http\Resources\GeographicalGuide\GeographicalSubCategoryResource;
use App\Models\GeographicalSubCategory;
use App\Utils\ApiResponse;
use App\Helpers\LangHelper;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GeographicalSubCategoryController extends Controller
{
    /**
     * Get all geographical sub-categories, optionally filtered by category
     */
    public function index(Request $request)
    {
        try {
            $query = GeographicalSubCategory::where('is_active', true)
                ->with('category')
                ->orderBy('id');

            // Filter by category if provided
            if ($request->has('geographical_category_id') && $request->geographical_category_id) {
                $query->where('geographical_category_id', $request->geographical_category_id);
            }

            $subCategories = $query->get();

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('geographical_sub_categories_fetched') ?? 'Geographical sub-categories fetched successfully',
                GeographicalSubCategoryResource::collection($subCategories)
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
     * Get single geographical sub-category
     */
    public function show(Request $request, $id)
    {
        try {
            $subCategory = GeographicalSubCategory::with('category')->find($id);

            if (!$subCategory) {
                return ApiResponse::send(
                    Response::HTTP_NOT_FOUND,
                    LangHelper::msg('geographical_sub_category_not_found') ?? 'Geographical sub-category not found',
                    []
                );
            }

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('geographical_sub_category_fetched') ?? 'Geographical sub-category fetched successfully',
                new GeographicalSubCategoryResource($subCategory)
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

