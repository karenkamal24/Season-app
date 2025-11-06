<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'item_id' => $this->id,
            'name' => $lang === 'ar' ? $this->name_ar : $this->name_en,
            'default_weight' => round($this->default_weight, 2),
            'weight_unit' => $this->weight_unit ?? 'kg',
            'category_id' => $this->category_id,
            'icon' => $this->icon,
            'description' => $lang === 'ar' ? $this->description_ar : $this->description_en,
        ];
    }
}
