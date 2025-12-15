<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\EmergencyNumber;

class EmergencyNumbersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            // Arab Countries
            ['name_ar' => 'مصر', 'name_en' => 'Egypt', 'code' => 'EGY', 'fire' => '180', 'police' => '122', 'ambulance' => '123', 'embassy' => '0227920000'],
            ['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'code' => 'SAU', 'fire' => '998', 'police' => '999', 'ambulance' => '997', 'embassy' => '0114644555'],
            ['name_ar' => 'الإمارات', 'name_en' => 'UAE', 'code' => 'ARE', 'fire' => '997', 'police' => '999', 'ambulance' => '998', 'embassy' => '026171100'],
            ['name_ar' => 'الكويت', 'name_en' => 'Kuwait', 'code' => 'KWT', 'fire' => '101', 'police' => '112', 'ambulance' => '112', 'embassy' => '0222340000'],
            ['name_ar' => 'قطر', 'name_en' => 'Qatar', 'code' => 'QAT', 'fire' => '999', 'police' => '999', 'ambulance' => '999', 'embassy' => '044457000'],
            ['name_ar' => 'البحرين', 'name_en' => 'Bahrain', 'code' => 'BHR', 'fire' => '999', 'police' => '999', 'ambulance' => '999', 'embassy' => '17585555'],
           
        ];

        foreach ($countries as $countryData) {
            $country = Country::updateOrCreate(
                ['code' => $countryData['code']],
                [
                    'name_ar' => $countryData['name_ar'],
                    'name_en' => $countryData['name_en'],
                ]
            );

            EmergencyNumber::updateOrCreate(
                ['country_id' => $country->id],
                [
                    'fire' => $countryData['fire'],
                    'police' => $countryData['police'],
                    'ambulance' => $countryData['ambulance'],
                    'embassy' => $countryData['embassy'],
                ]
            );
        }
    }
}
