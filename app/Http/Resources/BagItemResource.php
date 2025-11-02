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
        $weight = $this->custom_weight ?? $this->item->default_weight;
        $totalWeight = $weight * $this->quantity;

        return [
            'item_id' => $this->item_id,
            'name' => $this->item->name_en,
            'name_arabic' => $this->item->name_ar,
            'category' => $this->item->category->name_en ?? null,
            'category_arabic' => $this->item->category->name_ar ?? null,
            'quantity' => $this->quantity,
            'weight_per_item' => round($weight, 2),
            'total_weight' => round($totalWeight, 2),
            'icon' => $this->item->icon,
        ];
    }
}

