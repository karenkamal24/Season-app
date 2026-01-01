<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name_en' => 'Clothing',
                'name_ar' => 'ملابس',
                'icon' => 'shirt',
                'icon_color' => '#3B82F6',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name_en' => 'Shoes',
                'name_ar' => 'أحذية',
                'icon' => 'shoe',
                'icon_color' => '#8B5CF6',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name_en' => 'Electronics',
                'name_ar' => 'إلكترونيات',
                'icon' => 'laptop',
                'icon_color' => '#10B981',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name_en' => 'Medicine & Care',
                'name_ar' => 'أدوية وعناية',
                'icon' => 'medical',
                'icon_color' => '#EF4444',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name_en' => 'Documents',
                'name_ar' => 'مستندات',
                'icon' => 'document',
                'icon_color' => '#F59E0B',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name_en' => 'Toiletries',
                'name_ar' => 'أدوات نظافة',
                'icon' => 'spray',
                'icon_color' => '#06B6D4',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name_en' => 'Accessories',
                'name_ar' => 'إكسسوارات',
                'icon' => 'watch',
                'icon_color' => '#EC4899',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name_en' => 'Books & Entertainment',
                'name_ar' => 'كتب وترفيه',
                'icon' => 'book',
                'icon_color' => '#6366F1',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name_en' => 'Food & Snacks',
                'name_ar' => 'طعام ووجبات خفيفة',
                'icon' => 'food',
                'icon_color' => '#F97316',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name_en' => 'Other',
                'name_ar' => 'أخرى',
                'icon' => 'dots',
                'icon_color' => '#6B7280',
                'sort_order' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ItemCategory::updateOrCreate(
                ['name_en' => $category['name_en']],
                $category
            );
        }

        $this->command->info('Item categories seeded successfully!');
    }
}
