<?php

namespace App\Helpers;

class LanguageHelper
{
    /**
     * Get current locale safely
     */
    public static function getLocale(): string
    {
        try {
            return app()->getLocale() ?? config('app.locale', 'en');
        } catch (\Exception $e) {
            return config('app.locale', 'en');
        }
    }
    
    /**
     * Check if current locale is Arabic
     */
    public static function isArabic(): bool
    {
        return self::getLocale() === 'ar';
    }
    
    /**
     * Get translation for navigation groups
     */
    public static function getNavigationGroup(string $group): string
    {
        $translations = [
            'settings' => ['ar' => 'الإعدادات', 'en' => 'Settings'],
            'country_cities' => ['ar' => 'الدول والمدن', 'en' => 'Country & Cities'],
            'emergency_numbers' => ['ar' => 'أرقام الطوارئ', 'en' => 'Emergency Numbers'],
            'customers' => ['ar' => 'العملاء', 'en' => 'Customers'],
        ];
        
        $locale = self::getLocale();
        return $translations[$group][$locale] ?? $translations[$group]['en'] ?? $group;
    }
    
    /**
     * Get translation for resources
     */
    public static function translate(string $key, array $translations): string
    {
        $locale = self::getLocale();
        return $translations[$locale] ?? $translations['en'] ?? $key;
    }
}

