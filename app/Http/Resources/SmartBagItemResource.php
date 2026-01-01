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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'weight' => (float) $this->weight,
            'total_weight' => $this->total_weight,
            'category' => $this->category,
            'category_en' => $this->getCategoryInEnglish($this->category),
            'essential' => $this->essential,
            'packed' => $this->packed,
            'quantity' => $this->quantity,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }

    /**
     * Get category in English
     *
     * @param string $category
     * @return string
     */
    protected function getCategoryInEnglish(string $category): string
    {
        $categories = [
            'ملابس' => 'Clothing',
            'أحذية' => 'Shoes',
            'إلكترونيات' => 'Electronics',
            'أدوية وعناية' => 'Medicine & Care',
            'مستندات' => 'Documents',
            'أخرى' => 'Other',
        ];

        return $categories[$category] ?? $category;
    }
}

