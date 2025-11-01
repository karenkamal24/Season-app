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
            ['name_ar' => 'عمان', 'name_en' => 'Oman', 'code' => 'OMN', 'fire' => '999', 'police' => '9999', 'ambulance' => '9999', 'embassy' => '24683700'],
            ['name_ar' => 'الأردن', 'name_en' => 'Jordan', 'code' => 'JOR', 'fire' => '911', 'police' => '911', 'ambulance' => '911', 'embassy' => '065593721'],
            ['name_ar' => 'لبنان', 'name_en' => 'Lebanon', 'code' => 'LBN', 'fire' => '175', 'police' => '112', 'ambulance' => '140', 'embassy' => '01433411'],
            ['name_ar' => 'سوريا', 'name_en' => 'Syria', 'code' => 'SYR', 'fire' => '113', 'police' => '112', 'ambulance' => '110', 'embassy' => '0113343100'],
            ['name_ar' => 'العراق', 'name_en' => 'Iraq', 'code' => 'IRQ', 'fire' => '115', 'police' => '104', 'ambulance' => '122', 'embassy' => '07901405555'],
            ['name_ar' => 'اليمن', 'name_en' => 'Yemen', 'code' => 'YEM', 'fire' => '191', 'police' => '194', 'ambulance' => '191', 'embassy' => '01438472'],
            ['name_ar' => 'السودان', 'name_en' => 'Sudan', 'code' => 'SDN', 'fire' => '998', 'police' => '999', 'ambulance' => '999', 'embassy' => '0183217777'],
            ['name_ar' => 'الجزائر', 'name_en' => 'Algeria', 'code' => 'DZA', 'fire' => '14', 'police' => '1548', 'ambulance' => '14', 'embassy' => '061601600'],
            ['name_ar' => 'المغرب', 'name_en' => 'Morocco', 'code' => 'MAR', 'fire' => '15', 'police' => '19', 'ambulance' => '15', 'embassy' => '0537660000'],
            ['name_ar' => 'تونس', 'name_en' => 'Tunisia', 'code' => 'TUN', 'fire' => '198', 'police' => '197', 'ambulance' => '190', 'embassy' => '71264811'],
            ['name_ar' => 'ليبيا', 'name_en' => 'Libya', 'code' => 'LBY', 'fire' => '1515', 'police' => '1515', 'ambulance' => '1515', 'embassy' => '0213346500'],
            ['name_ar' => 'فلسطين', 'name_en' => 'Palestine', 'code' => 'PSE', 'fire' => '102', 'police' => '100', 'ambulance' => '101', 'embassy' => '0229707777'],

            // Other Popular Countries
            ['name_ar' => 'الولايات المتحدة', 'name_en' => 'United States', 'code' => 'USA', 'fire' => '911', 'police' => '911', 'ambulance' => '911', 'embassy' => '2025015000'],
            ['name_ar' => 'المملكة المتحدة', 'name_en' => 'United Kingdom', 'code' => 'GBR', 'fire' => '999', 'police' => '999', 'ambulance' => '999', 'embassy' => '02074998000'],
            ['name_ar' => 'كندا', 'name_en' => 'Canada', 'code' => 'CAN', 'fire' => '911', 'police' => '911', 'ambulance' => '911', 'embassy' => '1613614000'],
            ['name_ar' => 'أستراليا', 'name_en' => 'Australia', 'code' => 'AUS', 'fire' => '000', 'police' => '000', 'ambulance' => '000', 'embassy' => '0262113300'],
            ['name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'code' => 'DEU', 'fire' => '112', 'police' => '110', 'ambulance' => '112', 'embassy' => '03022763000'],
            ['name_ar' => 'فرنسا', 'name_en' => 'France', 'code' => 'FRA', 'fire' => '18', 'police' => '17', 'ambulance' => '15', 'embassy' => '0170962000'],
            ['name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'code' => 'ITA', 'fire' => '115', 'police' => '113', 'ambulance' => '118', 'embassy' => '064946256'],
            ['name_ar' => 'إسبانيا', 'name_en' => 'Spain', 'code' => 'ESP', 'fire' => '080', 'police' => '091', 'ambulance' => '061', 'embassy' => '0190432000'],
            ['name_ar' => 'روسيا', 'name_en' => 'Russia', 'code' => 'RUS', 'fire' => '101', 'police' => '102', 'ambulance' => '103', 'embassy' => '07509553000'],
            ['name_ar' => 'الصين', 'name_en' => 'China', 'code' => 'CHN', 'fire' => '119', 'police' => '110', 'ambulance' => '120', 'embassy' => '861085321700'],
            ['name_ar' => 'اليابان', 'name_en' => 'Japan', 'code' => 'JPN', 'fire' => '119', 'police' => '110', 'ambulance' => '119', 'embassy' => '0358121000'],
            ['name_ar' => 'الهند', 'name_en' => 'India', 'code' => 'IND', 'fire' => '101', 'police' => '100', 'ambulance' => '102', 'embassy' => '01124198000'],
            ['name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'code' => 'PAK', 'fire' => '16', 'police' => '15', 'ambulance' => '115', 'embassy' => '0518430000'],
            ['name_ar' => 'تركيا', 'name_en' => 'Turkey', 'code' => 'TUR', 'fire' => '110', 'police' => '155', 'ambulance' => '112', 'embassy' => '0312420000'],
            ['name_ar' => 'جنوب أفريقيا', 'name_en' => 'South Africa', 'code' => 'ZAF', 'fire' => '10177', 'police' => '10111', 'ambulance' => '10177', 'embassy' => '0124224242'],
            ['name_ar' => 'نيجيريا', 'name_en' => 'Nigeria', 'code' => 'NGA', 'fire' => '112', 'police' => '199', 'ambulance' => '199', 'embassy' => '0146163000'],
            ['name_ar' => 'كينيا', 'name_en' => 'Kenya', 'code' => 'KEN', 'fire' => '999', 'police' => '999', 'ambulance' => '999', 'embassy' => '020217000'],
            ['name_ar' => 'البرازيل', 'name_en' => 'Brazil', 'code' => 'BRA', 'fire' => '193', 'police' => '190', 'ambulance' => '192', 'embassy' => '556132128400'],
            ['name_ar' => 'الأرجنتين', 'name_en' => 'Argentina', 'code' => 'ARG', 'fire' => '100', 'police' => '101', 'ambulance' => '107', 'embassy' => '1143772000'],
            ['name_ar' => 'المكسيك', 'name_en' => 'Mexico', 'code' => 'MEX', 'fire' => '068', 'police' => '060', 'ambulance' => '065', 'embassy' => '52552081300'],
            ['name_ar' => 'كوريا الجنوبية', 'name_en' => 'South Korea', 'code' => 'KOR', 'fire' => '119', 'police' => '112', 'ambulance' => '119', 'embassy' => '0286350000'],
            ['name_ar' => 'تايلاند', 'name_en' => 'Thailand', 'code' => 'THA', 'fire' => '199', 'police' => '191', 'ambulance' => '1669', 'embassy' => '022052177'],
            ['name_ar' => 'ماليزيا', 'name_en' => 'Malaysia', 'code' => 'MYS', 'fire' => '994', 'police' => '999', 'ambulance' => '999', 'embassy' => '0321698800'],
            ['name_ar' => 'سنغافورة', 'name_en' => 'Singapore', 'code' => 'SGP', 'fire' => '995', 'police' => '999', 'ambulance' => '995', 'embassy' => '64761388'],
            ['name_ar' => 'إندونيسيا', 'name_en' => 'Indonesia', 'code' => 'IDN', 'fire' => '113', 'police' => '110', 'ambulance' => '118', 'embassy' => '0215241109'],
            ['name_ar' => 'الفلبين', 'name_en' => 'Philippines', 'code' => 'PHL', 'fire' => '160', 'police' => '117', 'ambulance' => '117', 'embassy' => '027901000'],
            ['name_ar' => 'فيتنام', 'name_en' => 'Vietnam', 'code' => 'VNM', 'fire' => '114', 'police' => '113', 'ambulance' => '115', 'embassy' => '02438509392'],
            ['name_ar' => 'نيوزيلندا', 'name_en' => 'New Zealand', 'code' => 'NZL', 'fire' => '111', 'police' => '111', 'ambulance' => '111', 'embassy' => '044730000'],
            ['name_ar' => 'هولندا', 'name_en' => 'Netherlands', 'code' => 'NLD', 'fire' => '112', 'police' => '09008844', 'ambulance' => '112', 'embassy' => '03105991111'],
            ['name_ar' => 'بلجيكا', 'name_en' => 'Belgium', 'code' => 'BEL', 'fire' => '112', 'police' => '101', 'ambulance' => '112', 'embassy' => '025050500'],
            ['name_ar' => 'سويسرا', 'name_en' => 'Switzerland', 'code' => 'CHE', 'fire' => '118', 'police' => '117', 'ambulance' => '144', 'embassy' => '0313050000'],
            ['name_ar' => 'النمسا', 'name_en' => 'Austria', 'code' => 'AUT', 'fire' => '122', 'police' => '133', 'ambulance' => '144', 'embassy' => '0165051000'],
            ['name_ar' => 'السويد', 'name_en' => 'Sweden', 'code' => 'SWE', 'fire' => '112', 'police' => '112', 'ambulance' => '112', 'embassy' => '0858504000'],
            ['name_ar' => 'النرويج', 'name_en' => 'Norway', 'code' => 'NOR', 'fire' => '110', 'police' => '112', 'ambulance' => '113', 'embassy' => '23312400'],
            ['name_ar' => 'الدنمارك', 'name_en' => 'Denmark', 'code' => 'DNK', 'fire' => '112', 'police' => '114', 'ambulance' => '112', 'embassy' => '35431374'],
            ['name_ar' => 'فنلندا', 'name_en' => 'Finland', 'code' => 'FIN', 'fire' => '112', 'police' => '112', 'ambulance' => '112', 'embassy' => '96968781'],
            ['name_ar' => 'بولندا', 'name_en' => 'Poland', 'code' => 'POL', 'fire' => '998', 'police' => '997', 'ambulance' => '999', 'embassy' => '0225050000'],
            ['name_ar' => 'جمهورية التشيك', 'name_en' => 'Czech Republic', 'code' => 'CZE', 'fire' => '150', 'police' => '158', 'ambulance' => '155', 'embassy' => '2575324000'],
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
