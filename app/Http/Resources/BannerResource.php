<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class BannerResource extends JsonResource
{
    public function toArray($request)
    {
        $locale = $this->getLocale($request);

        return [
            'id' => $this->id,
            'image' => $this->getImageUrl(),
            'country' => $this->getCountryData($locale),
            'language' => $this->language ?? 'ar',
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    private function getLocale($request): string
    {
        $acceptLanguage = $request->header('Accept-Language', 'ar');
        $locale = strtolower(explode('-', $acceptLanguage)[0]);
        return in_array($locale, ['ar', 'en']) ? $locale : 'ar';
    }

    private function getCountryData(string $locale): ?array
    {
        if (!$this->relationLoaded('country') || !$this->country) {
            return null;
        }

        $countryName = $locale === 'ar'
            ? ($this->country->name_ar ?? $this->country->name_en ?? 'Unknown')
            : ($this->country->name_en ?? $this->country->name_ar ?? 'Unknown');

        return [
            'id' => $this->country->id,
            'code' => $this->country->code ?? null,
            'name' => $countryName,
        ];
    }

    /**
     * Generate final public URL for banner image
     */
    private function getImageUrl(): ?string
    {
        try {
            if (!$this->image) return null;

            // Remove "public/" if present
            $path = str_replace('public/', '', $this->image);

            // Return valid URL like: http://localhost:8000/storage/banners/file.png
            return asset('storage/' . $path);

        } catch (\Exception $e) {
            Log::error("Error generating banner image URL", [
                'banner_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
