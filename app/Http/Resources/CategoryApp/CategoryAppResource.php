<?php

namespace App\Http\Resources\CategoryApp;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryAppResource extends JsonResource
{
    public function toArray($request): array
    {
        $lang = app()->getLocale();

        return [
            'id' => $this->id,
            'name' => $lang === 'ar' ? ($this->name_ar ?? $this->name_en) : ($this->name_en ?? $this->name_ar),
            'icon' => $this->icon_url,
            'url' => $this->url,
            'is_active' => $this->is_active,
        ];
    }
}

