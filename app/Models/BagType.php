<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BagType extends Model
{
    protected $fillable = [
        'name_en',
        'name_ar',
        'code',
        'description_en',
        'description_ar',
        'default_max_weight',
        'is_active',
    ];

    protected $casts = [
        'default_max_weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function travelBags(): HasMany
    {
        return $this->hasMany(TravelBag::class);
    }
}
