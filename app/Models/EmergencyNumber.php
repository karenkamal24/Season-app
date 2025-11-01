<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmergencyNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'fire',
        'police',
        'ambulance',
        'embassy'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
