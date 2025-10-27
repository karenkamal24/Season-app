<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'latitude',
        'longitude',
        'distance_from_center',
        'is_within_radius',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_from_center' => 'decimal:2',
        'is_within_radius' => 'boolean',
    ];

    /**
     * Group relationship
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

