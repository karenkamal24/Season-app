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
            ->with(['category', 'countries']);

        // Filter by country code (single country)
        if ($countryCode) {
            $country = Country::where('code', strtoupper(trim($countryCode)))->first();
            
            if (!$country) {
                // If country code is invalid, return empty collection
                return collect([]);
            }
            
            $query->whereHas('countries', function ($q) use ($country) {
                $q->where('countries.id', $country->id);
            });
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
            ->with(['category', 'countries']);

        // Filter by country code (single country) if provided
        if ($countryCode) {
            $country = Country::where('code', strtoupper(trim($countryCode)))->first();
            
            if (!$country) {
                return null;
            }
            
            $query->whereHas('countries', function ($q) use ($country) {
                $q->where('countries.id', $country->id);
            });
        }

        return $query->find($id);
    }
}

