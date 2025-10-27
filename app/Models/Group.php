<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'invite_code',
        'qr_code',
        'safety_radius',
        'notifications_enabled',
        'is_active',
    ];

    protected $casts = [
        'safety_radius' => 'integer',
        'notifications_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Group owner
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Group members (pivot table)
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'status', 'is_within_radius', 'out_of_range_count', 'joined_at', 'last_location_update')
            ->withTimestamps();
    }

    /**
     * Active members only
     */
    public function activeMembers()
    {
        return $this->members()->wherePivot('status', 'active');
    }

    /**
     * Members out of range
     */
    public function outOfRangeMembers()
    {
        return $this->members()
            ->wherePivot('status', 'active')
            ->wherePivot('is_within_radius', false);
    }

    /**
     * Group locations history
     */
    public function locations()
    {
        return $this->hasMany(GroupLocation::class);
    }

    /**
     * Latest location for each member
     */
    public function latestLocations()
    {
        return $this->hasMany(GroupLocation::class)
            ->latest('updated_at')
            ->groupBy('user_id');
    }

    /**
     * SOS alerts
     */
    public function sosAlerts()
    {
        return $this->hasMany(GroupSosAlert::class);
    }

    /**
     * Active SOS alerts
     */
    public function activeSosAlerts()
    {
        return $this->sosAlerts()->where('status', 'active');
    }

    /**
     * Group member records
     */
    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }
}

