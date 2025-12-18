<?php

namespace App\Imports;

use App\Models\GeographicalGuide;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class GeographicalGuidesImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $countryId;
    protected $cityId;
    protected $geographicalCategoryId;
    protected $geographicalSubCategoryId;

    public function __construct($countryId, $cityId, $geographicalCategoryId, $geographicalSubCategoryId)
    {
        $this->countryId = $countryId;
        $this->cityId = $cityId;
        $this->geographicalCategoryId = $geographicalCategoryId;
        $this->geographicalSubCategoryId = $geographicalSubCategoryId;
    }

    public function model(array $row)
    {
        return new GeographicalGuide([
            'user_id' => Auth::check() ? Auth::id() : null,
            'country_id' => $this->countryId,
            'city_id' => $this->cityId,
            'geographical_category_id' => $this->geographicalCategoryId,
            'geographical_sub_category_id' => $this->geographicalSubCategoryId,
            'service_name' => $row['service_name'] ?? null,
            'description' => $row['description'] ?? null,
            'phone_1' => $row['phone_1'] ?? null,
            'phone_2' => $row['phone_2'] ?? null,
            'address' => $row['address'] ?? null,
            'latitude' => isset($row['latitude']) ? (float)$row['latitude'] : null,
            'longitude' => isset($row['longitude']) ? (float)$row['longitude'] : null,
            'website' => $row['website'] ?? null,
            'commercial_register' => $row['commercial_register'] ?? null,
            'is_active' => true,
            'status' => 'approved',
        ]);
    }

    public function rules(): array
    {
        return [
            'service_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'phone_1' => 'nullable|string|max:255',
            'phone_2' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'website' => 'nullable|url|max:255',
            'commercial_register' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'status' => 'nullable|string|max:255',
        ];
    }
}


