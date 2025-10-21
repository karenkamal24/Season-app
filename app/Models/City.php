<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar', 'country_id'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
