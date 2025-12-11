<?php

namespace App\Services;

use App\Models\Banner;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Helpers\LangHelper;

class BannerService
{
    /**
     * Get active banners by language
     */
    public function getActiveByLanguage(?string $language)
    {
        if (!$language) {
            return collect([]);
        }

        $language = strtolower($language);
        if (!in_array($language, ['en', 'ar'])) {
            return collect([]);
        }

        return Banner::where('language', $language)
            ->where('is_active', true)
            ->get();
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

