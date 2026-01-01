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

        // Calculate weight
        $weight = $this->weight;
        $totalWeight = $weight * $this->quantity;

        // Get category information
        $category = null;
        if ($this->itemCategory) {
            $category = [
                'id' => $this->itemCategory->id,
                'name' => $lang === 'ar' ? $this->itemCategory->name_ar : $this->itemCategory->name_en,
                'icon' => $this->itemCategory->icon,
                'icon_color' => $this->itemCategory->icon_color,
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'weight' => round($weight, 2),
            'quantity' => $this->quantity,
            'total_weight' => round($totalWeight, 2),
            'item_category_id' => $this->item_category_id,
            'category' => $category,
            'essential' => $this->essential,
            'packed' => $this->packed,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

