<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmbassiesSeeder extends Seeder
{
    public function run(): void
    {
        $hosts = [
            'SAU' => ['الرياض', 'المملكة العربية السعودية'],
            'ARE' => ['أبوظبي', 'الإمارات العربية المتحدة'],
            'KWT' => ['الكويت', 'الكويت'],
            'BHR' => ['المنامة', 'البحرين'],
            'OMN' => ['مسقط', 'سلطنة عُمان'],
            'QAT' => ['الدوحة', 'قطر'],
            'EGY' => ['القاهرة', 'مصر'],
        ];
        $represented = [
            'EGY' => 'جمهورية مصر العربية',
            'SAU' => 'المملكة العربية السعودية',
            'ARE' => 'الإمارات العربية المتحدة',
        ];

        foreach ($hosts as $hostCode => [$city, $hostName]) {
            foreach ($represented as $representedCode => $representedName) {
                if ($representedCode === $hostCode || ($hostCode === 'EGY' && $representedCode !== 'SAU')) {
                    continue;
                }

                DB::table('embassies')->updateOrInsert(
                    [
                        'country_represented_code' => $representedCode,
                        'host_country_code' => $hostCode,
                    ],
                    [
                        'name' => "سفارة {$representedName} في {$city}",
                        'phone' => 'PLACEHOLDER - VERIFY PHONE',
                        'address' => "PLACEHOLDER - VERIFY ADDRESS ({$city}، {$hostName})",
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }
}
