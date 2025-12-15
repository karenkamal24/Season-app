<?php

namespace App\Http\Controllers\Api\DigitalDirectory;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Services\CategoryService;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\LangHelper;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Get all categories
     * Uses Accept-Language header for localization
     */
    public function index(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));

        // Set locale
        if (in_array($lang, ['ar', 'en'])) {
            app()->setLocale($lang);
        }

        $categories = $this->categoryService->getAllCategories();

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('categories_fetched'),
            CategoryResource::collection($categories)
        );
    }

    /**
     * Get single category by ID
     */
    public function show(Request $request, $id)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));

        // Set locale
        if (in_array($lang, ['ar', 'en'])) {
            app()->setLocale($lang);
        }

        $category = $this->categoryService->getCategoryById($id);

        if (!$category) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                LangHelper::msg('category_not_found')
            );
        }

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('category_fetched'),
            new CategoryResource($category)
        );
    }
}

