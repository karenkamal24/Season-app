<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'trip_type',
        'duration',
        'destination',
        'departure_date',
        'max_weight',
        'total_weight',
        'status',
        'preferences',
        'is_analyzed',
        'last_analyzed_at',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'max_weight' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'preferences' => 'array',
        'is_analyzed' => 'boolean',
        'last_analyzed_at' => 'datetime',
    ];

    protected $appends = [
        'weight_percentage',
        'remaining_weight',
        'is_overweight',
        'days_until_departure',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BagItem::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(BagAnalysis::class);
    }

    public function latestAnalysis(): HasOne
    {
        return $this->hasOne(BagAnalysis::class)->latestOfMany();
    }

    // Accessors
    public function getWeightPercentageAttribute(): float
    {
        if ($this->max_weight == 0) return 0;
        return round(($this->total_weight / $this->max_weight) * 100, 2);
    }

    public function getRemainingWeightAttribute(): float
    {
        return max(0, $this->max_weight - $this->total_weight);
    }

    public function getIsOverweightAttribute(): bool
    {
        return $this->total_weight > $this->max_weight;
    }

    public function getDaysUntilDepartureAttribute(): int
    {
        return now()->diffInDays($this->departure_date, false);
    }

    // Methods
    public function recalculateWeight(): void
    {
        $this->total_weight = $this->items()->get()->sum(function ($item) {
            return $item->weight * $item->quantity;
        });
        $this->save();
    }

    public function markAsAnalyzed(): void
    {
        $this->update([
            'is_analyzed' => true,
            'last_analyzed_at' => now(),
        ]);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('departure_date', '>=', now())
                    ->orderBy('departure_date', 'asc');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeNeedsAlert($query, int $hoursThreshold = 24)
    {
        return $query->where('departure_date', '<=', now()->addHours($hoursThreshold))
                    ->where('departure_date', '>=', now())
                    ->where('status', 'in_progress');
    }
}
