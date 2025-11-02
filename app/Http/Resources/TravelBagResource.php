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
        return [
            'bag_id' => $this->id,
            'bag_name' => $this->name_ar ?? $this->bagType->name_ar ?? 'شنطة الشحن الرئيسية',
            'bag_type' => $this->bagType->code ?? 'main_cargo',
            'current_weight' => round($this->current_weight, 2),
            'max_weight' => round($this->max_weight, 2),
            'weight_unit' => $this->weight_unit,
            'weight_percentage' => round($this->weight_percentage, 2),
            'items' => BagItemResource::collection($this->whenLoaded('bagItems')),
            'is_empty' => $this->bagItems->isEmpty(),
        ];
    }
}
