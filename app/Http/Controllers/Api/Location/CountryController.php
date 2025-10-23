<?php

namespace App\Http\Controllers\Api\Location;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Services\CountryService;
use App\Utils\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use App\Helpers\LangHelper;

class CountryController extends Controller
{
    public function __construct(
        protected CountryService $countryService
    ) {}

    public function index(Request $request)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));

        $countries = $this->countryService->getAllCountries();

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('countries_fetched'),
            CountryResource::collection($countries)->additional(['lang' => $lang])
        );
    }

    public function show(Request $request, $id)
    {
        $lang = strtolower($request->header('Accept-Language', 'en'));
        $country = $this->countryService->getCountryById($id);

        if (!$country) {
            return ApiResponse::send(Response::HTTP_NOT_FOUND, LangHelper::msg('country_not_found'));
        }

        return ApiResponse::send(
            Response::HTTP_OK,
            LangHelper::msg('country_fetched'),
            (new CountryResource($country))->additional(['lang' => $lang])
        );
    }
}
