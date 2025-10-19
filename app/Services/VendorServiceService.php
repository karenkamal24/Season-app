<?php

namespace App\Services;

use App\Models\VendorService;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VendorServiceService
{

  public function store($request): VendorService
{
    $user = Auth::user();

    $maxSetting = Setting::where('name->en', 'Service Provider')->first();

    if ($maxSetting && $user->vendor_services()->count() >= $maxSetting->max) {
        throw new HttpException(403, 'You have reached the maximum number of vendor services allowed.');
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

    return VendorService::create([
        'user_id' => $user->id,
        'service_type_id' => $request->service_type_id,
        'name' => $request->name,
        'description' => $request->description,
        'contact_number' => $request->contact_number,
        'address' => $request->address,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'commercial_register' => $commercialPath,
        'images' => $images,
        'status' => 'pending',
    ]);
}


    public function getMyServices()
    {
        return Auth::user()
            ->vendor_services()
            ->latest()
            ->get();
    }


    public function show($id): VendorService
    {
        $service = VendorService::where('user_id', Auth::id())->find($id);

        if (! $service) {
            throw new HttpException(404, 'Vendor service not found.');
        }

        return $service;
    }


public function update($request, $id): VendorService
{
    $service = VendorService::where('user_id', Auth::id())->find($id);

    if (! $service) {
        throw new HttpException(404, 'Vendor service not found.');
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

    $service->update($data);

    return $service;
}


public function delete($id): void
{
    $service = VendorService::where('user_id', Auth::id())->find($id);

    if (! $service) {
        throw new HttpException(404, 'Vendor service not found.');
    }
    if (in_array($service->status, ['pending', 'approved'])) {
        $service->update(['status' => 'disabled']);
    } else {
        throw new HttpException(403, 'This service cannot be deleted.');
    }
}

}
