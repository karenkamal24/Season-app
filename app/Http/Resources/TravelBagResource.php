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
        $lang = app()->getLocale();
        $bagType = $this->whenLoaded('bagType') ?? $this->bagType;
        $bagItems = $this->whenLoaded('bagItems') ?? $this->bagItems ?? collect([]);
        $tripReminder = $this->relationLoaded('tripReminder')
            ? $this->tripReminder
            : null;

        $reminderData = null;
        if ($tripReminder) {
            $date = $tripReminder->date
                ? $tripReminder->date->format('Y-m-d')
                : null;

            $timeValue = $tripReminder->time;
            if (is_string($timeValue)) {
                if (preg_match('/^(\d{1,2}):(\d{2})/', $timeValue, $matches)) {
                    $time = $matches[1] . ':' . $matches[2];
                } else {
                    $time = $timeValue;
                }
            } elseif (is_object($timeValue) && method_exists($timeValue, 'format')) {
                $time = $timeValue->format('H:i');
            } else {
                $time = null;
            }

            $reminderData = [
                'date' => $date,
                'time' => $time,
            ];
        }

        return [
            'bag_id' => $this->id,
            'bag_name' => $lang === 'ar'
                ? ($this->name_ar ?? ($bagType ? $bagType->name_ar : null) ?? 'شنطة الشحن الرئيسية')
                : ($this->name_en ?? ($bagType ? $bagType->name_en : null) ?? 'Main Cargo Bag'),
            'bag_type' => $bagType ? $bagType->id : ($this->bag_type_id ?? 1), // Default to ID 1 (main cargo)
            'bag_type_id' => $this->bag_type_id,
            'current_weight' => round($this->current_weight, 2),
            'max_weight' => round($this->max_weight, 2),
            'weight_unit' => $this->weight_unit,
            'weight_percentage' => round($this->weight_percentage, 2),
            'items' => BagItemResource::collection($bagItems),
            'is_empty' => $bagItems->isEmpty(),
            'reminder' => $reminderData,
            // في الـ API: لو في reminder -> ready, لو null -> not_ready
            'status' => $reminderData ? 'ready' : 'not_ready',
            'is_ready' => (bool) $reminderData,
        ];
    }
}
