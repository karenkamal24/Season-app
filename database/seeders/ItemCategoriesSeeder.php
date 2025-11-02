<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_en' => 'Boarding',
                'name_ar' => 'الصعود',
                'icon' => 'plane',
                'icon_color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name_en' => 'Funds',
                'name_ar' => 'أموال',
                'icon' => 'credit-card',
                'icon_color' => '#10B981',
                'sort_order' => 2,
            ],
            [
                'name_en' => 'Personal Essentials',
                'name_ar' => 'أساسيات شخصية',
                'icon' => 'user',
                'icon_color' => '#F59E0B',
                'sort_order' => 3,
            ],
            [
                'name_en' => 'Entertainment',
                'name_ar' => 'ترفيه',
                'icon' => 'music-note',
                'icon_color' => '#8B5CF6',
                'sort_order' => 4,
            ],
            [
                'name_en' => 'Electronics',
                'name_ar' => 'إلكترونيات',
                'icon' => 'bolt',
                'icon_color' => '#EF4444',
                'sort_order' => 5,
            ],
            [
                'name_en' => 'Clothing',
                'name_ar' => 'ملابس',
                'icon' => 'shirt',
                'icon_color' => '#EC4899',
                'sort_order' => 6,
            ],
            [
                'name_en' => 'Toiletries',
                'name_ar' => 'أدوات نظافة',
                'icon' => 'sparkles',
                'icon_color' => '#06B6D4',
                'sort_order' => 7,
            ],
            [
                'name_en' => 'Accessories',
                'name_ar' => 'إكسسوارات',
                'icon' => 'tag',
                'icon_color' => '#64748B',
                'sort_order' => 8,
            ],
            [
                'name_en' => 'First Aids',
                'name_ar' => 'إسعافات أولية',
                'icon' => 'plus-circle',
                'icon_color' => '#DC2626',
                'sort_order' => 9,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\ItemCategory::firstOrCreate(
                ['name_en' => $category['name_en']],
                $category
            );
        }
    }
}
