<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeographicalCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subCategories()
    {
        return $this->hasMany(GeographicalSubCategory::class);
    }

    public function guides()
    {
        return $this->hasMany(GeographicalGuide::class);
    }

    public function getLocalizedNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getIconUrlAttribute(): ?string
    {
        if (!$this->icon) {
            return null;
        }

        if (str_starts_with($this->icon, 'http')) {
            return $this->icon;
        }

        return asset('storage/' . $this->icon);
    }
}
