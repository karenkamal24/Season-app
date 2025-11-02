<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BagTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\BagType::firstOrCreate(
            ['code' => 'main_cargo'],
            [
                'name_en' => 'Main Cargo Bag',
                'name_ar' => 'شنطة الشحن الرئيسية',
                'code' => 'main_cargo',
                'description_en' => 'Large suitcase for checked luggage',
                'description_ar' => 'شنطة كبيرة للأمتعة المشحونة',
                'default_max_weight' => 23.0,
                'is_active' => true,
            ]
        );

        \App\Models\BagType::firstOrCreate(
            ['code' => 'travel_bag'],
            [
                'name_en' => 'Travel Bag',
                'name_ar' => 'شنطة سفر',
                'code' => 'travel_bag',
                'description_en' => 'Large suitcase for checked luggage',
                'description_ar' => 'شنطة كبيرة للأمتعة المشحونة',
                'default_max_weight' => 23.0,
                'is_active' => true,
            ]
        );

        \App\Models\BagType::firstOrCreate(
            ['code' => 'backpack'],
            [
                'name_en' => 'Backpack',
                'name_ar' => 'شنطة ظهر',
                'code' => 'backpack',
                'description_en' => 'Hand luggage that goes on the plane',
                'description_ar' => 'حقيبة اليد التي تصعد بها الطائرة',
                'default_max_weight' => 8.0,
                'is_active' => true,
            ]
        );
    }
}
