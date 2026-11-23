<?php

namespace App\Services;

use App\Models\Country;
use App\Models\EmergencyNumber;
use Illuminate\Support\Facades\DB;

class EmergencyService
{
    /**
     * Get emergency numbers by ISO alpha-3 country code.
     */
    public function getEmergencyByCountry(?string $countryCode): ?EmergencyNumber
    {
        if (!$countryCode) {
            return null;
        }

        return EmergencyNumber::with('country')
            ->where('country_code', strtoupper(trim($countryCode)))
            ->first();
    }

    public function getEmbassyFor(?string $nationality, string $hostCountryCode): ?array
    {
        if (!$nationality) {
            return null;
        }

        $nationality = strtoupper(trim($nationality));
        if ($nationality === $hostCountryCode) {
            return null;
        }

        $embassy = DB::table('embassies')
            ->where('country_represented_code', $nationality)
            ->where('host_country_code', $hostCountryCode)
            ->first();

        if (!$embassy) {
            return null;
        }

        return [
            'country_represented' => Country::where('code', $nationality)->value('name_ar'),
            'name' => $embassy->name,
            'phone' => $embassy->phone,
            'address' => $embassy->address,
        ];
    }
}
