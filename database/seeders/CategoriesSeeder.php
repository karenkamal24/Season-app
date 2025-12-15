<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Country;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define categories based on the image
        $categories = [
            [
                'name_ar' => 'التعليم',
                'name_en' => 'Education',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'النقل والمواصلات',
                'name_en' => 'Transportation',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'التوصيل',
                'name_en' => 'Delivery',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'الإقامة والفنادق',
                'name_en' => 'Accommodation & Hotels',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'الصحة والعافية',
                'name_en' => 'Health & Wellness',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'الطعام',
                'name_en' => 'Food',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'الترفيه والألعاب',
                'name_en' => 'Entertainment & Games',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'الخدمات الحكومية',
                'name_en' => 'Government Services',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'المال',
                'name_en' => 'Money',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'البودكاست',
                'name_en' => 'Podcast',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'الرياضة واللياقة',
                'name_en' => 'Sports & Fitness',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'التسوق',
                'name_en' => 'Shopping',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'المستعمل',
                'name_en' => 'Used/Second-hand',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'name_ar' => 'الترجمة',
                'name_en' => 'Translation',
                'icon' => null,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                [
                    'name_ar' => $categoryData['name_ar'],
                ],
                [
                    'name_en' => $categoryData['name_en'],
                    'icon' => $categoryData['icon'],
                    'is_active' => $categoryData['is_active'],
                ]
            );
        }
    }
}

