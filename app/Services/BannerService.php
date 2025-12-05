<?php

namespace App\Services;

use App\Models\Banner;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Helpers\LangHelper;

class BannerService
{
    /**
     * Get active banner by country code and language
     */
    public function getActiveByCountryAndLanguage(?string $countryCode, ?string $language): ?Banner
    {
        if (!$countryCode || !$language) {
            return null;
        }

        // Find country by code (supports multiple code formats)
        $countryCode = strtoupper($countryCode);

        // Map common variations to standard codes (KSA, UAE, EGY)
        $codeMapping = [
            'SAU' => 'KSA',
            'SA' => 'KSA',
            'KSA' => 'KSA',
            'ARE' => 'UAE',
            'AE' => 'UAE',
            'UAE' => 'UAE',
            'EGY' => 'EGY',
            'EG' => 'EGY',
        ];

        $standardCode = $codeMapping[$countryCode] ?? $countryCode;

        // Try to find country by mapped code
        $country = \App\Models\Country::where('code', $standardCode)->first();

        if (!$country) {
            return null;
        }

        $language = strtolower($language);
        if (!in_array($language, ['en', 'ar'])) {
            return null;
        }

        return Banner::where('country_id', $country->id)
            ->where('language', $language)
            ->where('is_active', true)
            ->with('country')
            ->first();
    }

    /**
     * Get banner by ID
     */
    public function getById(int $id): Banner
    {
        $banner = Banner::find($id);

        if (!$banner) {
            throw new HttpException(404, LangHelper::msg('banner_not_found'));
        }

        return $banner;
    }
}

