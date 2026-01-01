<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BagItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bag_id',
        'name',
        'weight',
        'item_category_id',
        'essential',
        'packed',
        'notes',
        'quantity',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'essential' => 'boolean',
        'packed' => 'boolean',
        'quantity' => 'integer',
    ];

    // Relationships
    public function bag(): BelongsTo
    {
        return $this->belongsTo(Bag::class);
    }

    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class);
    }

    // Accessors
    public function getTotalWeightAttribute(): float
    {
        return $this->weight * $this->quantity;
    }

    // Methods
    public function togglePacked(): void
    {
        $this->update(['packed' => !$this->packed]);
    }

    public function markAsEssential(): void
    {
        $this->update(['essential' => true]);
    }

    // Scopes
    public function scopeEssential($query)
    {
        return $query->where('essential', true);
    }

    public function scopePacked($query)
    {
        return $query->where('packed', true);
    }

    public function scopeUnpacked($query)
    {
        return $query->where('packed', false);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('item_category_id', $categoryId);
    }

    // Events
    protected static function booted(): void
    {
        static::created(function ($item) {
            $item->bag->recalculateWeight();
        });

        static::updated(function ($item) {
            if ($item->isDirty(['weight', 'quantity'])) {
                $item->bag->recalculateWeight();
            }
        });

        static::deleted(function ($item) {
            $item->bag->recalculateWeight();
        });
    }
}
