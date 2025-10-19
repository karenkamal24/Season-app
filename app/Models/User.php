<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nickname',
        'email',
        'phone',
        'photo_url',
        'address',
        'avatar',
        'lat',
        'lng',
        'city',
        'currency',
        'language',
        'provider',
        'provider_id',
        'role',
        'is_blocked',
        'email_verified_at',
        'password',
        'last_otp',
        'last_otp_expire',
        'request',
        'coins',
        'trips',
        'has_interests',
        'notification_token',
        'birth_date',
        'gender',
        'is_vendor'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'last_otp',
        'last_otp_expire',

    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_blocked' => 'boolean',
        'has_interests' => 'boolean',
        'request' => 'integer',
        'coins' => 'integer',
        'trips' => 'integer',
        'last_otp_expire' => 'datetime',
        'is_vendor' => 'boolean'
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin' && !$this->is_blocked;
    }


    public function vendor_services()
    {
        return $this->hasMany(\App\Models\VendorService::class, 'user_id');
    }
}
