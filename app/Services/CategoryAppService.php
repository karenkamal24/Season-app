<?php

namespace App\Services;

use App\Models\CategoryApp;
use App\Models\Country;
use Illuminate\Support\Collection;

class CategoryAppService
{
    /**
     * Get category apps by country code and optional category filter
     */
    public function getCategoryAppsByCountry(?string $countryCode, ?int $categoryId = null): Collection
    {
        $query = CategoryApp::where('is_active', true)
            ->with(['category', 'country']);

        // Filter by country code
        if ($countryCode) {
            $country = Country::where('code', strtoupper($countryCode))->first();
            if ($country) {
                $query->where('country_id', $country->id);
            } else {
                // If country code is invalid, return empty collection
                return collect([]);
            }
        }

        // Filter by category if provided
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->orderBy('name_ar')->get();
    }

    /**
     * Get category app by ID
     */
    public function getCategoryAppById($id, ?string $countryCode = null)
    {
        $query = CategoryApp::where('is_active', true)
            ->with(['category', 'country']);

        // Filter by country code if provided
        if ($countryCode) {
            $country = Country::where('code', strtoupper($countryCode))->first();
            if ($country) {
                $query->where('country_id', $country->id);
            } else {
                return null;
            }
        }

        return $query->find($id);
    }
}

