<?php

namespace App\Http\Controllers\Api\GeographicalGuide;

use App\Http\Controllers\Controller;
use App\Services\GeographicalGuideService;
use App\Http\Requests\GeographicalGuide\StoreGeographicalGuideRequest;
use App\Http\Requests\GeographicalGuide\IndexGeographicalGuideRequest;
use App\Http\Resources\GeographicalGuide\GeographicalGuideResource;
use App\Utils\ApiResponse;
use App\Helpers\LangHelper;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class GeographicalGuideController extends Controller
{
    protected $geographicalGuideService;

    public function __construct(GeographicalGuideService $geographicalGuideService)
    {
        $this->geographicalGuideService = $geographicalGuideService;
    }

    /**
     * Get all geographical guides with filters
     */
    public function index(IndexGeographicalGuideRequest $request)
    {
        try {
            $geographicalGuides = $this->geographicalGuideService->index($request);

            return ApiResponse::send(
                Response::HTTP_OK,
                LangHelper::msg('geographical_guides_fetched'),
                GeographicalGuideResource::collection($geographicalGuides)
            );
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : Response::HTTP_BAD_REQUEST;

            return ApiResponse::send(
                $statusCode,
                $e->getMessage(),
                []
            );
        }
    }

    /**
     * Store new geographical guide
     */
    public function store(StoreGeographicalGuideRequest $request)
    {
        try {
            $geographicalGuide = $this->geographicalGuideService->store($request);

            return ApiResponse::send(
                Response::HTTP_CREATED,
                LangHelper::msg('geographical_guide_created'),
                new GeographicalGuideResource($geographicalGuide)
            );
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : Response::HTTP_BAD_REQUEST;

            return ApiResponse::send(
                $statusCode,
                $e->getMessage(),
                []
            );
        }
    }
}

