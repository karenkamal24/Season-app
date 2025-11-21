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

        // Check if this is a custom item or regular item
        $isCustomItem = $this->item_id === null && $this->custom_item_name !== null;

        if ($isCustomItem) {
            // Custom item
            $weight = $this->custom_weight;
            $totalWeight = $weight * $this->quantity;

            return [
                'item_id' => null,
                'custom_item_name' => $this->custom_item_name,
                'name' => $this->custom_item_name,
                'category' => null,
                'quantity' => $this->quantity,
                'weight_per_item' => round($weight, 2),
                'total_weight' => round($totalWeight, 2),
                'is_custom' => true,
            ];
        } else {
            // Regular item - check if item relationship exists
            if (!$this->item) {
                // Fallback if item doesn't exist (shouldn't happen, but handle gracefully)
                return [
                    'item_id' => $this->item_id,
                    'custom_item_name' => null,
                    'name' => 'Unknown Item',
                    'category' => null,
                    'quantity' => $this->quantity,
                    'weight_per_item' => round($this->custom_weight ?? 0, 2),
                    'total_weight' => round(($this->custom_weight ?? 0) * $this->quantity, 2),
                    'is_custom' => false,
                ];
            }

            $weight = $this->custom_weight ?? $this->item->default_weight;
            $totalWeight = $weight * $this->quantity;

            return [
                'item_id' => $this->item_id,
                'custom_item_name' => null,
                'name' => $lang === 'ar' ? $this->item->name_ar : $this->item->name_en,
                'category' => $this->item->category ? (
                    $lang === 'ar'
                        ? ($this->item->category->name_ar ?? null)
                        : ($this->item->category->name_en ?? null)
                ) : null,
                'quantity' => $this->quantity,
                'weight_per_item' => round($weight, 2),
                'total_weight' => round($totalWeight, 2),
                'is_custom' => false,
            ];
        }
    }
}

