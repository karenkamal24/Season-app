<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BagAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'bag_id',
        'analysis_id',
        'missing_items',
        'extra_items',
        'weight_optimization',
        'additional_suggestions',
        'smart_alert',
        'metadata',
        'confidence_score',
        'processing_time_ms',
        'ai_model',
    ];

    protected $casts = [
        'missing_items' => 'array',
        'extra_items' => 'array',
        'weight_optimization' => 'array',
        'additional_suggestions' => 'array',
        'smart_alert' => 'array',
        'metadata' => 'array',
        'confidence_score' => 'decimal:2',
        'processing_time_ms' => 'integer',
    ];

    // Relationships
    public function bag(): BelongsTo
    {
        return $this->belongsTo(Bag::class);
    }

    // Boot method to auto-generate analysis_id
    protected static function booted(): void
    {
        static::creating(function ($analysis) {
            if (empty($analysis->analysis_id)) {
                $analysis->analysis_id = 'analysis_' . Str::random(10) . '_' . time();
            }
        });
    }

    // Accessors
    public function getMissingItemsCountAttribute(): int
    {
        return count($this->missing_items ?? []);
    }

    public function getExtraItemsCountAttribute(): int
    {
        return count($this->extra_items ?? []);
    }

    public function getSuggestionsCountAttribute(): int
    {
        return count($this->additional_suggestions ?? []);
    }

    public function getWeightSavedAttribute(): float
    {
        return $this->weight_optimization['weight_saved'] ?? 0;
    }

    // Methods
    public function hasHighPriorityAlerts(): bool
    {
        if (empty($this->smart_alert)) {
            return false;
        }

        return ($this->smart_alert['severity'] ?? '') === 'high';
    }

    public function getHighPriorityMissingItems(): array
    {
        if (empty($this->missing_items)) {
            return [];
        }

        return collect($this->missing_items)
            ->where('priority', 'high')
            ->all();
    }
}

