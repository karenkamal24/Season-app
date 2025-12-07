<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BannerService;
use App\Http\Resources\BannerResource;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\LangHelper;

class BannerController extends Controller
{
    protected BannerService $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    /**
     * Get active banner by language from headers
     */
    public function index(Request $request)
    {
        $language = $request->header('Accept-Language', 'ar');

        $banner = $this->bannerService->getActiveByLanguage($language);

        if (!$banner) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                LangHelper::msg('banner_not_found')
            );
        }

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('banner_fetched'),
            new BannerResource($banner)
        );
    }
}

