<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BagItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = app()->getLocale();
        $weight = $this->custom_weight ?? $this->item->default_weight;
        $totalWeight = $weight * $this->quantity;

        return [
            'item_id' => $this->item_id,
            'name' => $lang === 'ar' ? $this->item->name_ar : $this->item->name_en,
            'category' => $lang === 'ar'
                ? ($this->item->category->name_ar ?? null)
                : ($this->item->category->name_en ?? null),
            'quantity' => $this->quantity,
            'weight_per_item' => round($weight, 2),
            'total_weight' => round($totalWeight, 2),
            'icon' => $this->item->icon,
        ];
    }
}

