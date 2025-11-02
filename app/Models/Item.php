<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        'category_id',
        'name_en',
        'name_ar',
        'default_weight',
        'weight_unit',
        'icon',
        'description_en',
        'description_ar',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'default_weight' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class);
    }

    public function bagItems(): HasMany
    {
        return $this->hasMany(BagItem::class);
    }
}
