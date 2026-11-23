<?php

namespace App\Http\Controllers\Api\Emergency;

use App\Http\Controllers\Controller;
use App\Services\EmergencyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmergencyController extends Controller
{
    public function __construct(
        protected EmergencyService $emergencyService
    ) {}

    public function show(Request $request): JsonResponse
    {
        $countryCode = $request->query('country_code') ?: $request->header('Accept-Country');

        if (!is_string($countryCode) || trim($countryCode) === '') {
            return response()->json(['success' => false, 'message' => 'كود الدولة مطلوب.'], 400);
        }

        $countryCode = strtoupper(trim($countryCode));

        $emergency = $this->emergencyService->getEmergencyByCountry($countryCode);

        if (!$emergency) {
            return response()->json(['success' => false, 'message' => 'الدولة غير مدعومة أو لا توجد لها أرقام طوارئ.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'country_code' => $countryCode,
                'country_name' => $emergency->country->name_ar,
                'police' => $emergency->police,
                'ambulance' => $emergency->ambulance,
                'fire' => $emergency->fire,
                'unified_emergency' => $emergency->unified_emergency,
                'embassy' => $this->emergencyService->getEmbassyFor($request->user('sanctum')?->nationality, $countryCode),
            ],
        ]);
    }
}
