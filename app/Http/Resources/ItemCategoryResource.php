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
        $lang = app()->getLocale();

        return [
            'category_id' => $this->id,
            'name' => $lang === 'ar' ? $this->name_ar : $this->name_en,
            'icon' => $this->icon,
          
            'items' => ItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
