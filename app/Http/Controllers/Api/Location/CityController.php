<?php

namespace App\Http\Controllers\Api\Location;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Utils\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Helpers\LangHelper;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $cities = City::with('country')->get();

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('cities_fetched'),
            CityResource::collection($cities)->additional(['lang' => $lang])
        );
    }

    public function show(Request $request, $id)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $city = City::with('country')->find($id);

        if (!$city) {
            return ApiResponse::send(Response::HTTP_NOT_FOUND, LangHelper::msg('city_not_found'));
        }

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('city_fetched'),
            (new CityResource($city))->additional(['lang' => $lang])
        );
    }
}
