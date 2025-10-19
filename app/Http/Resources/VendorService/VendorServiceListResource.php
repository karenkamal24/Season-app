<?php

namespace App\Http\Resources\VendorService;

use Illuminate\Http\Resources\Json\JsonResource;

class VendorServiceListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->getLocalizedStatus(),
        ];
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
