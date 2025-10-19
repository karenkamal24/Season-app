<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\VendorServiceService;
use App\Http\Requests\VendorService\StoreVendorServiceRequest;
use App\Http\Requests\VendorService\UpdateVendorServiceRequest;
use App\Http\Resources\VendorService\VendorServiceResource;
use App\Http\Resources\VendorService\VendorServiceListResource;
use App\Utils\ApiResponse;
use App\Models\ServiceType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VendorServiceController extends Controller
{
    protected VendorServiceService $vendorServiceService;

    public function __construct(VendorServiceService $vendorServiceService)
    {
        $this->vendorServiceService = $vendorServiceService;
    }

    public function index()
    {
        $vendorServices = $this->vendorServiceService->getMyServices();

        return ApiResponse::send(
            Response::HTTP_OK,
            'Vendor services retrieved successfully.',
            VendorServiceListResource::collection($vendorServices)
        );
    }


    public function show($id)
    {
        $vendorService = $this->vendorServiceService->show($id);

        return ApiResponse::send(
            Response::HTTP_OK,
            'Vendor service details retrieved successfully.',
            new VendorServiceResource($vendorService)
        );
    }

    public function store(StoreVendorServiceRequest $request)
    {
        $vendorService = $this->vendorServiceService->store($request);

        return ApiResponse::send(
            Response::HTTP_CREATED,
            'Vendor service created successfully and is pending admin approval.',
            new VendorServiceResource($vendorService)
        );
    }

    public function update(UpdateVendorServiceRequest $request, $id)
    {
        $vendorService = $this->vendorServiceService->update($request, $id);

        return ApiResponse::send(
            Response::HTTP_OK,
            'Vendor service updated successfully.',
            new VendorServiceResource($vendorService)
        );
    }

    public function destroy($id)
    {
        $this->vendorServiceService->delete($id);

        return ApiResponse::send(
            Response::HTTP_OK,
            'Vendor service disabled successfully.'
        );
    }
    public function indexServiceType(Request $request)
    {
        $lang = $request->header('Accept-Language', 'ar');
        $types = ServiceType::where('is_active', true)->get();
        $data = $types->map(function ($type) use ($lang) {
            return [
                'id' => $type->id,
                'name' => $lang === 'ar' ? $type->name_ar : $type->name_en,
                'is_active' => (bool) $type->is_active,
            ];
        });

        return ApiResponse::send(
            Response::HTTP_OK,
            'Service types retrieved successfully.',
            $data
        );
    }
}
