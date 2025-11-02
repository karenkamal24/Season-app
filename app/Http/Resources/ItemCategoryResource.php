<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'category_id' => $this->id,
            'name' => $this->name_en,
            'name_arabic' => $this->name_ar,
            'icon' => $this->icon,
            'icon_color' => $this->icon_color,
            'items' => ItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
