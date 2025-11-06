<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelBagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale();
        $bagType = $this->whenLoaded('bagType') ?? $this->bagType;

        return [
            'bag_id' => $this->id,
            'bag_name' => $lang === 'ar'
                ? ($this->name_ar ?? ($bagType ? $bagType->name_ar : null) ?? 'شنطة الشحن الرئيسية')
                : ($this->name_en ?? ($bagType ? $bagType->name_en : null) ?? 'Main Cargo Bag'),
            'bag_type' => $bagType ? $bagType->id : 1, // Default to ID 1 (main cargo)
            'current_weight' => round($this->current_weight, 2),
            'max_weight' => round($this->max_weight, 2),
            'weight_unit' => $this->weight_unit,
            'weight_percentage' => round($this->weight_percentage, 2),
            'items' => BagItemResource::collection($this->whenLoaded('bagItems')),
            'is_empty' => $this->bagItems->isEmpty(),
        ];
    }
}
