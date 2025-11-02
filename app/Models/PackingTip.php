<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackingTip extends Model
{
    protected $fillable = [
        'text_en',
        'text_ar',
        'category',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];
}
