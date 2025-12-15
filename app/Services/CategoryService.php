<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    /**
     * Get all active categories
     */
    public function getAllCategories()
    {
        return Category::where('is_active', true)
            ->orderBy('name_ar')
            ->get();
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($id)
    {
        return Category::where('is_active', true)
            ->find($id);
    }
}

