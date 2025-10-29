<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'role',
        'status',
        'is_within_radius',
        'out_of_range_count',
        'joined_at',
        'last_location_update',
        'last_notification_sent_at',
    ];

    protected $casts = [
        'is_within_radius' => 'boolean',
        'out_of_range_count' => 'integer',
        'joined_at' => 'datetime',
        'last_location_update' => 'datetime',
        'last_notification_sent_at' => 'datetime',
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

    /**
     * All locations for this member in this group
     */
    public function locations()
    {
        return $this->hasMany(GroupLocation::class, 'user_id', 'user_id')
            ->latest('updated_at');
    }


}

