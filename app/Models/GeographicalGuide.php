<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeographicalGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'geographical_category_id',
        'geographical_sub_category_id',
        'service_name',
        'description',
        'phone_1',
        'phone_2',
        'country_id',
        'city_id',
        'address',
        'latitude',
        'longitude',
        'website',
        'commercial_register',
        'is_active',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(GeographicalCategory::class, 'geographical_category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(GeographicalSubCategory::class, 'geographical_sub_category_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
