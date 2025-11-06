<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BagTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \App\Models\BagType::query()->delete();

       
        \App\Models\BagType::create([
            'id' => 1,
            'name_en' => 'Main Cargo Bag',
            'name_ar' => 'شنطة الشحن الرئيسية',
            'description_en' => 'Large suitcase for checked luggage',
            'description_ar' => 'شنطة كبيرة للأمتعة المشحونة',
            'default_max_weight' => 23.0,
            'is_active' => true,
        ]);

        \App\Models\BagType::create([
            'id' => 2,
            'name_en' => 'Backpack',
            'name_ar' => 'شنطة ظهر',
            'description_en' => 'Hand luggage that goes on the plane',
            'description_ar' => 'حقيبة اليد التي تصعد بها الطائرة',
            'default_max_weight' => 8.0,
            'is_active' => true,
        ]);
    }
}
