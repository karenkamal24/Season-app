<?php

namespace App\Http\Resources\GeographicalGuide;

use Illuminate\Http\Resources\Json\JsonResource;

class GeographicalCategoryResource extends JsonResource
{
    public function toArray($request): array
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
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'name' => $isArabic ? $this->name_ar : $this->name_en,
            'icon' => $this->icon_url,
            'is_active' => $this->is_active,
            'sub_categories' => GeographicalSubCategoryResource::collection($this->whenLoaded('subCategories')),
        ];
    }
}

