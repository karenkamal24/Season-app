<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\City;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear old data - disable foreign key checks first
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        City::truncate();
        Country::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            // Saudi Arabia - السعودية
            [
                'code' => 'KSA',
                'name_en' => 'Saudi Arabia',
                'name_ar' => 'السعودية',
                'cities' => [
                    ['name_en' => 'Riyadh', 'name_ar' => 'الرياض'],
                    ['name_en' => 'Jeddah', 'name_ar' => 'جدة'],
                    ['name_en' => 'Mecca', 'name_ar' => 'مكة المكرمة'],
                    ['name_en' => 'Medina', 'name_ar' => 'المدينة المنورة'],
                    ['name_en' => 'Dammam', 'name_ar' => 'الدمام'],
                    ['name_en' => 'Khobar', 'name_ar' => 'الخبر'],
                    ['name_en' => 'Dhahran', 'name_ar' => 'الظهران'],
                    ['name_en' => 'Taif', 'name_ar' => 'الطائف'],
                    ['name_en' => 'Tabuk', 'name_ar' => 'تبوك'],
                    ['name_en' => 'Buraidah', 'name_ar' => 'بريدة'],
                    ['name_en' => 'Khamis Mushait', 'name_ar' => 'خميس مشيط'],
                    ['name_en' => 'Abha', 'name_ar' => 'أبها'],
                    ['name_en' => 'Najran', 'name_ar' => 'نجران'],
                    ['name_en' => 'Jazan', 'name_ar' => 'جازان'],
                    ['name_en' => 'Yanbu', 'name_ar' => 'ينبع'],
                    ['name_en' => 'Al Ahsa', 'name_ar' => 'الأحساء'],
                    ['name_en' => 'Hail', 'name_ar' => 'حائل'],
                    ['name_en' => 'Al Qatif', 'name_ar' => 'القطيف'],
                    ['name_en' => 'Jubail', 'name_ar' => 'الجبيل'],
                    ['name_en' => 'Al Kharj', 'name_ar' => 'الخرج'],
                ],
            ],

            // United Arab Emirates - الإمارات
            [
                'code' => 'UAE',
                'name_en' => 'United Arab Emirates',
                'name_ar' => 'الإمارات',
                'cities' => [
                    ['name_en' => 'Abu Dhabi', 'name_ar' => 'أبو ظبي'],
                    ['name_en' => 'Dubai', 'name_ar' => 'دبي'],
                    ['name_en' => 'Sharjah', 'name_ar' => 'الشارقة'],
                    ['name_en' => 'Ajman', 'name_ar' => 'عجمان'],
                    ['name_en' => 'Ras Al Khaimah', 'name_ar' => 'رأس الخيمة'],
                    ['name_en' => 'Fujairah', 'name_ar' => 'الفجيرة'],
                    ['name_en' => 'Umm Al Quwain', 'name_ar' => 'أم القيوين'],
                    ['name_en' => 'Al Ain', 'name_ar' => 'العين'],
                    ['name_en' => 'Khor Fakkan', 'name_ar' => 'خورفكان'],
                    ['name_en' => 'Dibba Al-Fujairah', 'name_ar' => 'دبا الفجيرة'],
                ],
            ],

            // Egypt - مصر
            [
                'code' => 'EGY',
                'name_en' => 'Egypt',
                'name_ar' => 'مصر',
                'cities' => [
                    ['name_en' => 'Cairo', 'name_ar' => 'القاهرة'],
                    ['name_en' => 'Giza', 'name_ar' => 'الجيزة'],
                    ['name_en' => 'Alexandria', 'name_ar' => 'الإسكندرية'],
                    ['name_en' => 'Shubra El Kheima', 'name_ar' => 'شبرا الخيمة'],
                    ['name_en' => 'Port Said', 'name_ar' => 'بورسعيد'],
                    ['name_en' => 'Suez', 'name_ar' => 'السويس'],
                    ['name_en' => 'Luxor', 'name_ar' => 'الأقصر'],
                    ['name_en' => 'Aswan', 'name_ar' => 'أسوان'],
                    ['name_en' => 'Asyut', 'name_ar' => 'أسيوط'],
                    ['name_en' => 'Ismailia', 'name_ar' => 'الإسماعيلية'],
                    ['name_en' => 'Faiyum', 'name_ar' => 'الفيوم'],
                    ['name_en' => 'Zagazig', 'name_ar' => 'الزقازيق'],
                    ['name_en' => 'Damietta', 'name_ar' => 'دمياط'],
                    ['name_en' => 'Mansoura', 'name_ar' => 'المنصورة'],
                    ['name_en' => 'Tanta', 'name_ar' => 'طنطا'],
                    ['name_en' => 'Beni Suef', 'name_ar' => 'بني سويف'],
                    ['name_en' => 'Sohag', 'name_ar' => 'سوهاج'],
                    ['name_en' => 'Hurghada', 'name_ar' => 'الغردقة'],
                    ['name_en' => 'Sharm El Sheikh', 'name_ar' => 'شرم الشيخ'],
                    ['name_en' => 'Minya', 'name_ar' => 'المنيا'],
                    ['name_en' => 'Qena', 'name_ar' => 'قنا'],
                    ['name_en' => 'Banha', 'name_ar' => 'بنها'],
                    ['name_en' => 'Kafr El Sheikh', 'name_ar' => 'كفر الشيخ'],
                    ['name_en' => 'Marsa Matruh', 'name_ar' => 'مرسى مطروح'],
                    ['name_en' => 'New Valley', 'name_ar' => 'الوادي الجديد'],
                    ['name_en' => 'North Sinai', 'name_ar' => 'شمال سيناء'],
                    ['name_en' => 'South Sinai', 'name_ar' => 'جنوب سيناء'],
                    ['name_en' => 'Red Sea', 'name_ar' => 'البحر الأحمر'],
                ],
            ],
        ];

        foreach ($data as $countryData) {
            $country = Country::updateOrCreate(
                ['code' => $countryData['code']],
                [
                    'name_en' => $countryData['name_en'],
                    'name_ar' => $countryData['name_ar'],
                ]
            );

            foreach ($countryData['cities'] as $cityData) {
                City::updateOrCreate(
                    [
                        'country_id' => $country->id,
                        'name_en' => $cityData['name_en'],
                    ],
                    [
                        'name_ar' => $cityData['name_ar'],
                    ]
                );
            }
        }
    }
}
