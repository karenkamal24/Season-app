<?php

namespace App\Services;

use App\Models\Country;

class CountryService
{
    public function getAllCountries()
    {
        return Country::with('cities')->get();
    }

    public function getCountryById($id)
    {
        return Country::with('cities')->find($id);
    }
}
