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
    protected $countryId;

    public function __construct($categoryId, $countryId)
    {
        $this->categoryId = $categoryId;
        $this->countryId = $countryId;
    }

    public function model(array $row)
    {
        $categoryApp = CategoryApp::create([
            'category_id' => $this->categoryId,
            'name_ar' => $row['name_ar'] ?? null,
            'name_en' => $row['name_en'] ?? null,
            'url' => $row['url'] ?? null,
            'is_active' => isset($row['is_active']) ? (bool)$row['is_active'] : true,
        ]);

        // Attach country
        if ($this->countryId) {
            $categoryApp->countries()->attach($this->countryId);
        }

        return $categoryApp;
    }

    public function rules(): array
    {
        return [
            'name_ar' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'url' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}

