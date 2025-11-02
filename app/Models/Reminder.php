<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'date',
        'time',
        'timezone',
        'recurrence',
        'notes',
        'attachment',
        'status',
        'last_sent_at',
    ];

    protected $casts = [
        'date' => 'date',
        'last_sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
