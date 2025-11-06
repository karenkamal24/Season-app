<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BagTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale();

        return [
            'bag_type_id' => $this->id,
            'name' => $lang === 'ar' ? $this->name_ar : $this->name_en,
            'description' => $lang === 'ar' ? $this->description_ar : $this->description_en,
            'default_max_weight' => round($this->default_max_weight, 2),
            'is_active' => $this->is_active,
        ];
    }
}
