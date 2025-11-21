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
                ['Airline Check-In', 'تسجيل الوصول للطيران', 10],
                ['Boarding Pass', 'تذكرة الصعود', 10],
                ['Passport / Visa / ID', 'جواز سفر / تأشيرة / هوية', 10],
            ],
            'Funds' => [
                ['Cash', 'نقد', 9],
                ['Credit Card', 'بطاقة ائتمان', 9],
            ],
            'Personal Essentials' => [
                ['Water Bottle', 'زجاجة ماء', 8],
                ['Travel Pillow', 'وسادة سفر', 7],
                ['Medicines', 'أدوية', 9],
                ['Snacks', 'وجبات خفيفة', 7],
            ],
            'Electronics' => [
                ['Phone', 'هاتف محمول', 8],
                ['Charger', 'شاحن', 7],
                ['Laptop', 'حاسوب محمول', 8],
                ['Headphones', 'سماعات', 7],
                ['Camera', 'كاميرا', 7],
            ],
            'Clothing' => [
                ['T-Shirt', 'تي شيرت', 6],
                ['Pants', 'بنطلون', 6],
                ['Shoes', 'أحذية', 7],
            ],
            'Toiletries' => [
                ['Toothbrush', 'فرشاة أسنان', 6],
                ['Shampoo', 'شامبو', 6],
                ['Soap', 'صابون', 5],
            ],
            'Accessories' => [
                ['Hat', 'قبعة', 5],
                ['Bag', 'حقيبة', 6],
                ['Belt', 'حزام', 5],
            ],
            'First Aids' => [
                ['Bandages', 'ضمادات', 9],
                ['Pain Relievers', 'مسكنات ألم', 9],
            ],
        ];

        foreach ($data as $categoryName => $items) {
            foreach ($items as $index => [$name_en, $name_ar, $weight]) {
                Item::create([
                    'name_en' => $name_en,
                    'name_ar' => $name_ar,
                    'default_weight' => $weight,
                    'sort_order' => $index + 1,
                    'category_id' => $categories[$categoryName]->id,
                ]);
            }
        }

        $this->command->info('✅ All Items seeded successfully!');
    }
}
