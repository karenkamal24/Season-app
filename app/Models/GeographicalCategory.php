<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    protected static function booted()
    {
        static::updating(function ($category) {
            // Delete old icon file if icon is being changed or deleted
            if ($category->isDirty('icon')) {
                $oldIcon = $category->getOriginal('icon');
                if ($oldIcon && !str_starts_with($oldIcon, 'http')) {
                    // Only delete if it's a local file (not URL)
                    $filePath = $oldIcon;
                    if (Storage::disk('public')->exists($filePath)) {
                        Storage::disk('public')->delete($filePath);
                    }
                }
            }
        });

        static::deleting(function ($category) {
            // Delete icon file when category is deleted
            if ($category->icon && !str_starts_with($category->icon, 'http')) {
                $filePath = $category->icon;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
        });
    }

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
