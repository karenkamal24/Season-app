<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'code'];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function emergency()
    {
        return $this->hasOne(EmergencyNumber::class);
    }

    public function categoryApps()
    {
        return $this->hasMany(CategoryApp::class);
    }

    public function vendorServices()
    {
        return $this->hasMany(VendorService::class);
    }
}
