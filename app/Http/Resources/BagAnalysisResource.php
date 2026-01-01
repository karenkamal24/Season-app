<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BagAnalysisResource extends JsonResource
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
            'analysis_id' => $this->analysis_id,
            'bag_id' => $this->bag_id,
            'missing_items' => $this->missing_items ?? [],
            'missing_items_count' => $this->missing_items_count,
            'extra_items' => $this->extra_items ?? [],
            'extra_items_count' => $this->extra_items_count,
            'weight_optimization' => $this->weight_optimization ?? [],
            'weight_saved' => $this->weight_saved,
            'additional_suggestions' => $this->additional_suggestions ?? [],
            'suggestions_count' => $this->suggestions_count,
            'smart_alert' => $this->smart_alert,
            'has_high_priority_alerts' => $this->hasHighPriorityAlerts(),
            'high_priority_missing_items' => $this->getHighPriorityMissingItems(),
            'confidence_score' => (float) $this->confidence_score,
            'processing_time_ms' => $this->processing_time_ms,
            'ai_model' => $this->ai_model,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
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
                'analysis_version' => '1.0',
                'timestamp' => now()->toIso8601String(),
            ],
        ];
    }
}
