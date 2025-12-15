<?php

namespace App\Http\Controllers\Api\DigitalDirectory;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryApp\CategoryAppResource;
use App\Services\CategoryAppService;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\LangHelper;

class CategoryAppController extends Controller
{
    public function __construct(
        protected CategoryAppService $categoryAppService
    ) {}

    /**
     * Get category apps
     * Uses Accept-Language and Accept-Country headers
     * Requires category_id filter
     */
    public function index(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $countryCode = $request->header('Accept-Country');
        $categoryId = $request->query('category_id');

        // Set locale
        if (in_array($lang, ['ar', 'en'])) {
            app()->setLocale($lang);
        }

        // Validate country code is provided
        if (!$countryCode) {
            return ApiResponse::send(
                Response::HTTP_BAD_REQUEST,
                LangHelper::msg('country_code_required')
            );
        }

        // Validate category_id is provided
        if (!$categoryId) {
            return ApiResponse::send(
                Response::HTTP_BAD_REQUEST,
                LangHelper::msg('category_id_required')
            );
        }

        $categoryApps = $this->categoryAppService->getCategoryAppsByCountry($countryCode, $categoryId);

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('category_apps_fetched'),
            CategoryAppResource::collection($categoryApps)
        );
    }

    /**
     * Get single category app by ID
     */
    public function show(Request $request, $id)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $countryCode = $request->header('Accept-Country');

        // Set locale
        if (in_array($lang, ['ar', 'en'])) {
            app()->setLocale($lang);
        }

        $categoryApp = $this->categoryAppService->getCategoryAppById($id, $countryCode);

        if (!$categoryApp) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                LangHelper::msg('category_app_not_found')
            );
        }

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('category_app_fetched'),
            new CategoryAppResource($categoryApp)
        );
    }
}

