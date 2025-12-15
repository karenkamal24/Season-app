<?php

namespace App\Imports;

use App\Models\CategoryApp;
use App\Models\Category;
use App\Models\Country;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class CategoryAppsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $categoryId;
    protected $countryIds;

    public function __construct($categoryId, $countryIds)
    {
        $this->categoryId = $categoryId;
        $this->countryIds = is_array($countryIds) ? $countryIds : ($countryIds ? [$countryIds] : []);
    }

    public function model(array $row)
    {
        $categoryApp = CategoryApp::create([
            'category_id' => $this->categoryId,
            'name_ar' => $row['name_ar'] ?? null,
            'name_en' => $row['name_en'] ?? null,
            'icon' => $row['icon'] ?? null,
            'url' => $row['url'] ?? null,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ]);

        // Attach multiple countries
        if (!empty($this->countryIds)) {
            $categoryApp->countries()->attach($this->countryIds);
        }

        return $categoryApp;
    }

    public function rules(): array
    {
        return [
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}

