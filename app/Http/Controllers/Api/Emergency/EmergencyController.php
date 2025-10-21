<?php

namespace App\Http\Controllers\Api\Emergency;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmergencyResource;
use App\Services\EmergencyService;
use App\Utils\ApiResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
                'Missing Accept-Country header'
            );
        }

        $emergency = $this->emergencyService->getEmergencyByCountry($countryCode);

        if (!$emergency) {
            return ApiResponse::send(
                Response::HTTP_NOT_FOUND,
                'Emergency numbers not found for this country'
            );
        }

        return ApiResponse::send(
            Response::HTTP_OK,
            'Emergency numbers fetched successfully.',
            new EmergencyResource($emergency)
        );
    }
}
