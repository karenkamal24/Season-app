<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BannerResource extends JsonResource
{
    public function toArray($request)
    {
        $locale = strtolower($request->header('Accept-Language', 'ar'));
        $countryName = $this->country
            ? ($locale === 'ar' ? $this->country->name_ar : $this->country->name_en)
            : null;

        return [
            'id' => $this->id,
            'image' => $this->getImageUrl(),
            'country' => [
                'id' => $this->country?->id,
                'code' => $this->country?->code,
                'name' => $countryName,
            ],
            'language' => $this->language,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    private function getImageUrl(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }
}

