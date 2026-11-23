<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\EmergencyNumber;
use Illuminate\Database\Seeder;

class EmergencyNumbersSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            'SAU' => ['المملكة العربية السعودية', 'Saudi Arabia', '999', '997', '998', '911'],
            'ARE' => ['الإمارات العربية المتحدة', 'United Arab Emirates', '999', '998', '997', '999'],
            'EGY' => ['مصر', 'Egypt', '122', '123', '180', '112'],
            'KWT' => ['الكويت', 'Kuwait', '112', '112', '112', '112'],
            'BHR' => ['البحرين', 'Bahrain', '999', '999', '999', '999'],
            'OMN' => ['سلطنة عُمان', 'Oman', '9999', '9999', '9999', '9999'],
            'QAT' => ['قطر', 'Qatar', '999', '999', '999', '999'],
        ];

        foreach ($countries as $code => [$nameAr, $nameEn, $police, $ambulance, $fire, $unified]) {
            $legacyCode = ['SAU' => 'SAU ', 'ARE' => 'UAE'][$code] ?? null;
            $country = Country::where('code', $code)->first()
                ?? ($legacyCode ? Country::where('code', $legacyCode)->first() : null);

            if ($country) {
                $country->update(['code' => $code, 'name_ar' => $nameAr, 'name_en' => $nameEn]);
            } else {
                $country = Country::create(['code' => $code, 'name_ar' => $nameAr, 'name_en' => $nameEn]);
            }

            EmergencyNumber::updateOrCreate(
                ['country_id' => $country->id],
                [
                    'country_code' => $code,
                    'police' => $police,
                    'ambulance' => $ambulance,
                    'fire' => $fire,
                    'unified_emergency' => $unified,
                    'embassy' => null,
                ]
            );
        }
    }
}
