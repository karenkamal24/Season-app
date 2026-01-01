<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BagResource extends JsonResource
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
            'trip_type' => $this->trip_type,
            'trip_type_en' => $this->getTripTypeInEnglish($this->trip_type),
            'duration' => $this->duration,
            'destination' => $this->destination,
            'departure_date' => $this->departure_date->format('Y-m-d'),
            'max_weight' => (float) $this->max_weight,
            'total_weight' => (float) $this->total_weight,
            'weight_percentage' => $this->weight_percentage,
            'remaining_weight' => $this->remaining_weight,
            'is_overweight' => $this->is_overweight,
            'days_until_departure' => $this->days_until_departure,
            'status' => $this->status,
            'status_en' => $this->getStatusInEnglish($this->status),
            'preferences' => $this->preferences,
            'is_analyzed' => $this->is_analyzed,
            'last_analyzed_at' => $this->last_analyzed_at?->toIso8601String(),
            'items_count' => $this->whenCounted('items'),
            'items' => BagItemResource::collection($this->whenLoaded('items')),
            'latest_analysis' => new BagAnalysisResource($this->whenLoaded('latestAnalysis')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }

    /**
     * Get trip type in English
     *
     * @param string $tripType
     * @return string
     */
    protected function getTripTypeInEnglish(string $tripType): string
    {
        $types = [
            'عمل' => 'Business',
            'سياحة' => 'Tourism',
            'عائلية' => 'Family',
            'علاج' => 'Medical',
        ];

        return $types[$tripType] ?? $tripType;
    }

    /**
     * Get status in English
     *
     * @param string $status
     * @return string
     */
    protected function getStatusInEnglish(string $status): string
    {
        $statuses = [
            'draft' => 'Draft',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        return $statuses[$status] ?? $status;
    }

    /**
     * Get additional data to add to the resource response.
     *
     * @param Request $request
     * @return array
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'version' => '1.0',
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }
}
