<?php

namespace App\Http\Controllers\Api\Emergency;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmergencyResource;
use App\Services\EmergencyService;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\LangHelper;

class EmergencyController extends Controller
{
    public function __construct(
        protected EmergencyService $emergencyService
    ) {}

    public function show(Request $request)
    {
        $countryCode = $request->header('Accept-Country');

        if (!$countryCode) {
            return ApiResponse::send(
                Response::HTTP_BAD_REQUEST,
                LangHelper::msg('emergency_missing_header')
            );
        }

        $emergency = $this->emergencyService->getEmergencyByCountry($countryCode);

        if (!$emergency) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                LangHelper::msg('emergency_not_found')
            );
        }

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('emergency_fetched'),
            new EmergencyResource($emergency)
        );
    }
}
