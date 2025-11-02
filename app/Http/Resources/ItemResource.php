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
        return [
            'item_id' => $this->id,
            'name' => $this->name_en,
            'name_arabic' => $this->name_ar,
            'default_weight' => round($this->default_weight, 2),
            'weight_unit' => $this->weight_unit ?? 'kg',
            'category_id' => $this->category_id,
            'category' => new ItemCategoryResource($this->whenLoaded('category')),
            'icon' => $this->icon,
            'description' => $this->description_en,
            'description_arabic' => $this->description_ar,
        ];
    }
}
