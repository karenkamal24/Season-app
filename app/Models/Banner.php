<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'language',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the country that owns the banner
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    /**
     * Scope to get active banners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get languages list
     */
    public static function getLanguages(): array
    {
        return [
            'en' => 'English',
            'ar' => 'العربية'
        ];
    }

    /**
     * Get title for display in Filament
     */
    public function getTitleAttribute(): string
    {
        $countryName = $this->country
            ? (app()->getLocale() === 'ar' ? $this->country->name_ar : $this->country->name_en)
            : 'N/A';

        $languageName = $this->language === 'ar'
            ? (app()->getLocale() === 'ar' ? 'العربية' : 'Arabic')
            : (app()->getLocale() === 'ar' ? 'الإنجليزية' : 'English');

        return "{$countryName} - {$languageName}";
    }
}
