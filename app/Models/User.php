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
        'fcm_token',
        'preferred_language',
        'birth_date',
        'gender',
        'is_vendor',
        'last_active_at',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'last_otp',
        'last_otp_expire',
        'fcm_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_blocked' => 'boolean',
        'has_interests' => 'boolean',
        'request' => 'integer',
        'coins' => 'integer',
        'trips' => 'integer',
        'last_otp_expire' => 'datetime',
        'is_vendor' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin' && !$this->is_blocked;
    }


    public function vendor_services()
    {
        return $this->hasMany(\App\Models\VendorService::class, 'user_id');
    }

    /**
     * Check if user is online (active within last 5 minutes)
     */
    public function getIsOnlineAttribute(): bool
    {
        if (!$this->last_active_at) {
            return false;
        }

        return $this->last_active_at->diffInMinutes(now()) < 5;
    }

    /**
     * Get user status (online/offline)
     */

public function getStatusAttribute()
{
    $locale = app()->getLocale();

    if ($this->is_online) {
        return $locale === 'ar' ? 'متصل' : 'online';
    }

    return $locale === 'ar' ? 'غير متصل' : 'offline';
}


    /**
     * Get last seen text
     */
 /**
 * Get last seen text (localized)
 */
public function getLastSeenAttribute(): ?string
{
    if (!$this->last_active_at) {
        return null;
    }

    $locale = app()->getLocale();

    if ($this->is_online) {
        return $locale === 'ar' ? 'متصل الآن' : 'Online now';
    }

    $minutes = (int) $this->last_active_at->diffInMinutes(now());

    if ($minutes < 60) {
        return $locale === 'ar'
            ? "نشط منذ {$minutes} دقيقة"
            : "Active {$minutes} minute(s) ago";
    }

    $hours = (int) $this->last_active_at->diffInHours(now());
    if ($hours < 24) {
        return $locale === 'ar'
            ? "نشط منذ {$hours} ساعة"
            : "Active {$hours} hour(s) ago";
    }

    $days = (int) $this->last_active_at->diffInDays(now());
    return $locale === 'ar'
        ? "نشط منذ {$days} يوم"
        : "Active {$days} day(s) ago";
}

}
