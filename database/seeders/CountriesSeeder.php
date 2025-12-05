<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'code' => 'KSA',
                'name_en' => 'Saudi Arabia',
                'name_ar' => 'السعودية',
            ],
            [
                'code' => 'UAE',
                'name_en' => 'United Arab Emirates',
                'name_ar' => 'الإمارات',
            ],
            [
                'code' => 'EGY',
                'name_en' => 'Egypt',
                'name_ar' => 'مصر',
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                [
                    'name_en' => $country['name_en'],
                    'name_ar' => $country['name_ar'],
                ]
            );
        }
    }
}
