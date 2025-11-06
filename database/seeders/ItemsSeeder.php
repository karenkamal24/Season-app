<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use App\Models\ItemCategory;

class ItemsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Item::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = ItemCategory::all()->keyBy('name_en');

        if ($categories->isEmpty()) {
            $this->command->error('⚠️ Please run ItemCategoriesSeeder first!');
            return;
        }

        $data = [
            'Boarding' => [
                ['Airline Check-In', 'تسجيل الوصول للطيران', 10, 'https://cdn-icons-png.flaticon.com/512/633/633652.png'],
                ['Boarding Pass', 'تذكرة الصعود', 10, 'https://cdn-icons-png.flaticon.com/512/555/555417.png'],
                ['Passport / Visa / ID', 'جواز سفر / تأشيرة / هوية', 10, 'https://cdn-icons-png.flaticon.com/512/681/681494.png'],
            ],
            'Funds' => [
                ['Cash', 'نقد', 9, 'https://cdn-icons-png.flaticon.com/512/2331/2331943.png'],
                ['Credit Card', 'بطاقة ائتمان', 9, 'https://cdn-icons-png.flaticon.com/512/262/262008.png'],
            ],
            'Personal Essentials' => [
                ['Water Bottle', 'زجاجة ماء', 8, 'https://cdn-icons-png.flaticon.com/512/1147/1147930.png'],
                ['Travel Pillow', 'وسادة سفر', 7, 'https://cdn-icons-png.flaticon.com/512/1048/1048924.png'],
                ['Medicines', 'أدوية', 9, 'https://cdn-icons-png.flaticon.com/512/2966/2966327.png'],
                ['Snacks', 'وجبات خفيفة', 7, 'https://cdn-icons-png.flaticon.com/512/857/857681.png'],
            ],
            'Electronics' => [
                ['Phone', 'هاتف محمول', 8, 'https://cdn-icons-png.flaticon.com/512/15/15874.png'],
                ['Charger', 'شاحن', 7, 'https://cdn-icons-png.flaticon.com/512/1792/1792532.png'],
                ['Laptop', 'حاسوب محمول', 8, 'https://cdn-icons-png.flaticon.com/512/4725/4725414.png'],
                ['Headphones', 'سماعات', 7, 'https://cdn-icons-png.flaticon.com/512/2927/2927268.png'],
                ['Camera', 'كاميرا', 7, 'https://cdn-icons-png.flaticon.com/512/149/149699.png'],
            ],
            'Clothing' => [
                ['T-Shirt', 'تي شيرت', 6, 'https://cdn-icons-png.flaticon.com/512/892/892458.png'],
                ['Pants', 'بنطلون', 6, 'https://cdn-icons-png.flaticon.com/512/3532/3532886.png'],
                ['Shoes', 'أحذية', 7, 'https://cdn-icons-png.flaticon.com/512/891/891462.png'],
            ],
            'Toiletries' => [
                ['Toothbrush', 'فرشاة أسنان', 6, 'https://cdn-icons-png.flaticon.com/512/2767/2767305.png'],
                ['Shampoo', 'شامبو', 6, 'https://cdn-icons-png.flaticon.com/512/706/706195.png'],
                ['Soap', 'صابون', 5, 'https://cdn-icons-png.flaticon.com/512/2614/2614556.png'],
            ],
            'Accessories' => [
                ['Hat', 'قبعة', 5, 'https://cdn-icons-png.flaticon.com/512/892/892629.png'],
                ['Bag', 'حقيبة', 6, 'https://cdn-icons-png.flaticon.com/512/891/891407.png'],
                ['Belt', 'حزام', 5, 'https://cdn-icons-png.flaticon.com/512/892/892408.png'],
            ],
            'First Aids' => [
                ['Bandages', 'ضمادات', 9, 'https://cdn-icons-png.flaticon.com/512/2966/2966327.png'],
                ['Pain Relievers', 'مسكنات ألم', 9, 'https://cdn-icons-png.flaticon.com/512/2966/2966327.png'],
            ],
        ];

        foreach ($data as $categoryName => $items) {
            foreach ($items as $index => [$name_en, $name_ar, $weight, $icon]) {
                Item::create([
                    'name_en' => $name_en,
                    'name_ar' => $name_ar,
                    'default_weight' => $weight,
                    'icon' => $icon,
                    'sort_order' => $index + 1,
                    'category_id' => $categories[$categoryName]->id,
                ]);
            }
        }

        $this->command->info('✅ All Items seeded successfully with image icons!');
    }
}
