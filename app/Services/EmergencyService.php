<?php

namespace App\Services;

use App\Models\Country;
use App\Models\EmergencyNumber;

class EmergencyService
{
    /**
     * Get emergency numbers by country code (from header)
     */
    public function getEmergencyByCountry(?string $countryCode): ?EmergencyNumber
    {
        if (!$countryCode) {
            return null;
        }

        $country = Country::where('code', strtoupper($countryCode))
            ->with('emergency')
            ->first();

        return $country?->emergency;
    }
}
