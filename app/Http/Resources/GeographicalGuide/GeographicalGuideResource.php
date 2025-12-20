<?php

namespace App\Http\Resources\GeographicalGuide;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class GeographicalGuideResource extends JsonResource
{
    public function toArray($request)
    {
        // Get language from header or use app locale as fallback
        $headerLang = $request->header('Accept-Language');

        // Extract language code (handle formats like 'en', 'en-US', 'ar-SA', etc.)
        if ($headerLang) {
            $lang = strtolower(explode('-', $headerLang)[0]);
        } else {
            $lang = strtolower(app()->getLocale() ?? 'en');
        }

        // Only accept 'ar' or 'en', default to 'en' if invalid
        if (!in_array($lang, ['ar', 'en'])) {
            $lang = 'en';
        }

        $isArabic = $lang === 'ar';

        return [
            'id' => $this->id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name_ar' => $this->category->name_ar,
                'name_en' => $this->category->name_en,
                'name' => $isArabic ? $this->category->name_ar : $this->category->name_en,
                'icon' => $this->category->icon_url,
            ] : null,
            'sub_category' => $this->subCategory ? [
                'id' => $this->subCategory->id,
                'name_ar' => $this->subCategory->name_ar,
                'name_en' => $this->subCategory->name_en,
                'name' => $isArabic ? $this->subCategory->name_ar : $this->subCategory->name_en,
            ] : null,
            'service_name' => $this->service_name,
            'description' => $this->description,
            'phone_1' => $this->phone_1,
            'phone_2' => $this->phone_2,
            'country' => $this->country ? [
                'id' => $this->country->id,
                'name_ar' => $this->country->name_ar,
                'name_en' => $this->country->name_en,
                'name' => $isArabic ? $this->country->name_ar : $this->country->name_en,
                'code' => $this->country->code,
            ] : null,
            'city' => $this->city ? [
                'id' => $this->city->id,
                'name_ar' => $this->city->name_ar,
                'name_en' => $this->city->name_en,
                'name' => $isArabic ? $this->city->name_ar : $this->city->name_en,
            ] : null,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'website' => $this->website,
            'commercial_register' => $this->commercial_register
                ? $this->getCommercialRegisterUrl()
                : null,
            'establishment_number' => $this->establishment_number,
            'is_active' => $this->is_active,
            'status' => $this->getStatusLabel($isArabic),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    private function getCommercialRegisterUrl(): ?string
    {
        if (!$this->commercial_register) {
            return null;
        }

        if (str_starts_with($this->commercial_register, 'http')) {
            return $this->commercial_register;
        }

        return asset('storage/' . $this->commercial_register);
    }

    private function getStatusLabel(bool $isArabic): string
    {
        return match($this->status) {
            'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
            'approved' => $isArabic ? 'موافق عليها' : 'Approved',
            'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
            default => $this->status ?? ($isArabic ? 'غير محدد' : 'Not Set'),
        };
    }
}

