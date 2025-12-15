<?php

namespace App\Services;

use App\Models\VendorService;
use App\Models\Setting;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Helpers\LangHelper;

class VendorServiceService
{
    public function store($request): VendorService
    {
        $user = Auth::user();


        $maxSetting = Setting::where('name->en', 'Service Provider')->first();

        if ($maxSetting && $user->vendor_services()->count() >= $maxSetting->max) {
            throw new HttpException(403, LangHelper::msg('vendor_service_limit_reached'));
        }


        $commercialPath = null;
        if ($request->hasFile('commercial_register')) {
            $commercialPath = $request->file('commercial_register')->store('vendor_services/registers', 'public');
        }


        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $images[] = $file->store('vendor_services/images', 'public');
            }
        }


        $vendorService = VendorService::create([
            'user_id'             => $user->id,
            'service_type_id'     => $request->service_type_id,
            'name'                => $request->name,
            'description'         => $request->description,
            'contact_number'      => $request->contact_number,
            'address'             => $request->address,
            'latitude'            => $request->latitude,
            'longitude'           => $request->longitude,
            'country_id'          => $request->country_id,
            'commercial_register' => $commercialPath,
            'images'              => $images,
            'status'              => 'pending',
        ]);

        // Load the country relationship for the response
        $vendorService->load(['country', 'serviceType']);

        return $vendorService;
    }

    /**
     * Get all vendor services for the authenticated user
     * Returns all services regardless of status (pending, approved, rejected, disabled)
     * No country filter - returns all services for the user from all countries
     */
    public function getMyServices()
    {
        return VendorService::where('user_id', Auth::id())
            ->with(['country', 'serviceType'])
            ->latest()
            ->get();
    }

    public function show($id): VendorService
    {
        $service = VendorService::where('user_id', Auth::id())
            ->with(['country', 'serviceType'])
            ->find($id);

        if (!$service) {
            throw new HttpException(404, LangHelper::msg('vendor_service_not_found'));
        }

        return $service;
    }

    public function update($request, $id): VendorService
    {
        $service = VendorService::where('user_id', Auth::id())->find($id);

        if (!$service) {
            throw new HttpException(404, LangHelper::msg('vendor_service_not_found'));
        }

        $data = $request->validated();

        if ($request->hasFile('commercial_register')) {
            $data['commercial_register'] = $request->file('commercial_register')
                ->store('vendor_services/registers', 'public');
        }

        if ($request->hasFile('images')) {
            $data['images'] = [];
            foreach ($request->file('images') as $file) {
                $data['images'][] = $file->store('vendor_services/images', 'public');
            }
        }

        // If service is approved and being edited, set status back to pending for admin review
        if ($service->status === 'approved') {
            $data['status'] = 'pending';
        }

        $service->update($data);

        // Load the country relationship for the response
        $service->load(['country', 'serviceType']);

        return $service;
    }

    public function delete($id): void
    {
        $service = VendorService::where('user_id', Auth::id())->find($id);

        if (!$service) {
            throw new HttpException(404, LangHelper::msg('vendor_service_not_found'));
        }

        if (in_array($service->status, ['pending', 'approved'])) {
            $service->update(['status' => 'disabled']);
        } else {
            throw new HttpException(403, LangHelper::msg('vendor_service_cannot_delete'));
        }
    }

    public function enable($id): VendorService
    {
        $service = VendorService::where('user_id', Auth::id())->find($id);

        if (!$service) {
            throw new HttpException(404, LangHelper::msg('vendor_service_not_found'));
        }

        if ($service->status === 'disabled') {
            $service->update(['status' => 'pending']);
        } else {
            throw new HttpException(403, LangHelper::msg('vendor_service_already_active'));
        }

        return $service->refresh()->load(['country', 'serviceType']);
    }

    public function forceDelete($id): void
    {
        $service = VendorService::where('user_id', Auth::id())->find($id);

        if (!$service) {
            throw new HttpException(404, LangHelper::msg('vendor_service_not_found'));
        }

        // Delete all images associated with this service
        if (!empty($service->images)) {
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // Delete commercial register if exists
        if ($service->commercial_register) {
            Storage::disk('public')->delete($service->commercial_register);
        }

        // Permanently delete the service
        $service->delete();
    }

    public function getAllApprovedServices($request = null)
    {
        $query = VendorService::where('status', 'approved')
            ->with(['country', 'serviceType']);

        // Filter by Accept-Country header if provided (required)
        if ($request && $request->hasHeader('Accept-Country')) {
            $countryCode = strtoupper($request->header('Accept-Country'));
            $country = Country::where('code', $countryCode)->first();
            if ($country) {
                $query->where('country_id', $country->id);
            } else {
                // If country code is invalid, return empty collection
                return collect([]);
            }
        } else {
            // If Accept-Country header is missing, return empty collection
            return collect([]);
        }

        // Filter by service_type_id if provided
        if ($request && $request->has('service_type_id')) {
            $query->where('service_type_id', $request->service_type_id);
        }

        return $query->latest()->get();
    }

    public function getByCountryCode($countryCode)
    {
        $country = Country::where('code', strtoupper($countryCode))->first();

        if (!$country) {
            return collect([]);
        }

        return VendorService::where('country_id', $country->id)
            ->where('status', 'approved')
            ->with(['country', 'serviceType', 'user'])
            ->latest()
            ->get();
    }

    public function getOneApproved($id, $request = null): VendorService
    {
        $query = VendorService::where('status', 'approved')
            ->where('id', $id)
            ->with(['country', 'serviceType']);

        // Filter by Accept-Country header if provided
        if ($request && $request->hasHeader('Accept-Country')) {
            $countryCode = strtoupper($request->header('Accept-Country'));
            $country = Country::where('code', $countryCode)->first();
            if ($country) {
                $query->where('country_id', $country->id);
            } else {
                throw new HttpException(404, LangHelper::msg('vendor_service_not_found'));
            }
        }

        $service = $query->first();

        if (!$service) {
            throw new HttpException(404, LangHelper::msg('vendor_service_not_found'));
        }

        return $service;
    }
}
