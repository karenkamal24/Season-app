<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmartBagItemResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'weight' => (float) $this->weight,
            'total_weight' => $this->total_weight,
            'item_category_id' => $this->item_category_id,
            'category' => $this->itemCategory ? [
                'id' => $this->itemCategory->id,
                'name' => $lang === 'ar' ? $this->itemCategory->name_ar : $this->itemCategory->name_en,
                'name_ar' => $this->itemCategory->name_ar,
                'name_en' => $this->itemCategory->name_en,
                'icon' => $this->itemCategory->icon,
                'icon_color' => $this->itemCategory->icon_color,
            ] : null,
            'essential' => $this->essential,
            'packed' => $this->packed,
            'quantity' => $this->quantity,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}

