<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BagItem extends Model
{
    protected $fillable = [
        'travel_bag_id',
        'item_id',
        'quantity',
        'custom_weight',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'custom_weight' => 'decimal:2',
    ];

    public function travelBag(): BelongsTo
    {
        return $this->belongsTo(TravelBag::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
