<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackingTipsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tips = [
            [
                'text_en' => 'Put heavy items at the bottom',
                'text_ar' => 'ضع الأشياء الثقيلة في الأسفل',
                'category' => 'organization',
                'priority' => 1,
            ],
            [
                'text_en' => 'Wrap clothes instead of folding to save space',
                'text_ar' => 'لف الملابس بدلاً من طيها لتوفير المساحة',
                'category' => 'space_saving',
                'priority' => 2,
            ],
            [
                'text_en' => 'Keep valuables in hand luggage',
                'text_ar' => 'احتفظ بالأشياء القيمة في حقيبة اليد',
                'category' => 'security',
                'priority' => 3,
            ],
            [
                'text_en' => 'Check the allowed weight from the airline company',
                'text_ar' => 'تأكد من الوزن المسموح به من شركة الطيران',
                'category' => 'weight',
                'priority' => 4,
            ],
            [
                'text_en' => 'Use packing cubes for organization',
                'text_ar' => 'استخدم أكياس التعبئة للتنظيم',
                'category' => 'organization',
                'priority' => 5,
            ],
            [
                'text_en' => 'Roll your clothes to prevent wrinkles',
                'text_ar' => 'لف ملابسك لمنع التجاعيد',
                'category' => 'space_saving',
                'priority' => 6,
            ],
            [
                'text_en' => 'Place liquids in sealed bags',
                'text_ar' => 'ضع السوائل في أكياس محكمة الغلق',
                'category' => 'security',
                'priority' => 7,
            ],
        ];

        foreach ($tips as $tip) {
            \App\Models\PackingTip::create($tip);
        }
    }
}
