<?php

namespace App\Http\Resources\VendorService;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class VendorServiceIndexResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'service_type' => $this->serviceType?->getLocalizedNameAttribute(),
            'name' => $this->name,
            'description' => $this->description,
            'contact_number' => $this->contact_number,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'images' => $this->images
                ? collect($this->images)->map(fn ($img) => $this->getFullUrl($img))
                : [],
            'status' => $this->getLocalizedStatus(),
        ];
    }

    private function getFullUrl($path)
    {
        if (is_array($path)) {
            $path = reset($path);
        }

        if (!is_string($path) || empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return asset(Storage::url($path));
    }

    private function getLocalizedStatus(): string
    {
        $locale = app()->getLocale();

        $translations = [
            'pending'  => ['en' => 'Pending',  'ar' => 'قيد المراجعة'],
            'approved' => ['en' => 'Approved', 'ar' => 'تمت الموافقة'],
            'disabled' => ['en' => 'Disabled', 'ar' => 'معطلة'],
            'rejected' => ['en' => 'Rejected', 'ar' => 'مرفوضة'],
        ];

        $status = strtolower($this->status);

        return $translations[$status][$locale] ?? $this->status;
    }
}


