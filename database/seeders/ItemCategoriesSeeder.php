<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ItemCategory;

class ItemCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        // 🚫 أوقف مؤقتًا الـ foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 🧹 امسح البيانات القديمة
        ItemCategory::truncate();

        // ✅ أرجع تشغيل الـ foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            [
                'name_en' => 'Boarding',
                'name_ar' => 'الصعود',
                'icon' => 'https://cdn-icons-png.flaticon.com/512/190/190601.png',
                'sort_order' => 1,
            ],
            [
                'name_en' => 'Funds',
                'name_ar' => 'أموال',
                'icon' => 'https://cdn-icons-png.flaticon.com/512/2331/2331943.png',
                'sort_order' => 2,
            ],
            [
                'name_en' => 'Personal Essentials',
                'name_ar' => 'أساسيات شخصية',
                'icon' => 'https://cdn-icons-png.flaticon.com/512/706/706164.png',
                'sort_order' => 3,
            ],
            [
                'name_en' => 'Entertainment',
                'name_ar' => 'ترفيه',
                'icon' => 'https://cdn-icons-png.flaticon.com/512/727/727245.png',
                'sort_order' => 4,
            ],
            [
                'name_en' => 'Electronics',
                'name_ar' => 'إلكترونيات',
                'icon' => 'https://cdn-icons-png.flaticon.com/512/1041/1041916.png',
                'sort_order' => 5,
            ],
            [
                'name_en' => 'Clothing',
                'name_ar' => 'ملابس',
                'icon' => 'https://cdn-icons-png.flaticon.com/512/892/892458.png',
                'sort_order' => 6,
            ],
            [
                'name_en' => 'Toiletries',
                'name_ar' => 'أدوات نظافة',
                'icon' => 'https://cdn-icons-png.flaticon.com/512/706/706195.png',
                'sort_order' => 7,
            ],
            [
                'name_en' => 'Accessories',
                'name_ar' => 'إكسسوارات',
                'icon' => 'https://cdn-icons-png.flaticon.com/512/2921/2921822.png',
                'sort_order' => 8,
            ],
            [
                'name_en' => 'First Aids',
                'name_ar' => 'إسعافات أولية',
                'icon' => 'https://cdn-icons-png.flaticon.com/512/2966/2966327.png',
                'sort_order' => 9,
            ],
        ];

        foreach ($categories as $category) {
            ItemCategory::create($category);
        }
    }
}
