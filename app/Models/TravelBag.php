<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TravelBag extends Model
{
    protected $fillable = [
        'user_id',
        'bag_type_id',
        'name_en',
        'name_ar',
        'max_weight',
        'weight_unit',
        'is_active',
        'status',
    ];

    protected $casts = [
        'max_weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bagType(): BelongsTo
    {
        return $this->belongsTo(BagType::class);
    }

    public function bagItems(): HasMany
    {
        return $this->hasMany(BagItem::class);
    }

    public function tripReminder(): HasOne
    {
        return $this->hasOne(Reminder::class, 'travel_bag_id')
            ->where('status', 'active')
            ->where('recurrence', 'once');
    }

    // Calculate current total weight
    public function getCurrentWeightAttribute(): float
    {
        return $this->bagItems->sum(function ($bagItem) {
            $weight = $bagItem->custom_weight ?? $bagItem->item->default_weight;
            return $weight * $bagItem->quantity;
        });
    }

    // Calculate weight percentage
    public function getWeightPercentageAttribute(): float
    {
        if ($this->max_weight == 0) return 0;
        return round(($this->current_weight / $this->max_weight) * 100, 2);
    }

    /**
     * Determine if bag is ready based on weight or status
     * If status is 'ready', bag is considered ready even if weight is not full
     */
    public function getIsReadyAttribute(): bool
    {
        // لو الـ status = 'ready' (محدد من travel-date endpoint)، الشنطة جاهزة
        if ($this->status === 'ready') {
            return true;
        }
        
        // غير كده، الشنطة جاهزة لو الوزن كامل
        return $this->current_weight >= $this->max_weight;
    }
}
