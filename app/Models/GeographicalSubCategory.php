<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeographicalSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'geographical_category_id',
        'name_ar',
        'name_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(GeographicalCategory::class, 'geographical_category_id');
    }

    public function guides()
    {
        return $this->hasMany(GeographicalGuide::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }
}
