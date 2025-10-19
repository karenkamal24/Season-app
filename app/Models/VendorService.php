<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorService extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_type_id',
        'name',
        'description',
        'contact_number',
        'address',
        'latitude',
        'longitude',
        'commercial_register',
        'images',
        'status',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
protected static function booted()
{
    static::updated(function ($service) {

        if ($service->isDirty('status') && $service->status === 'approved') {
            $setting = \App\Models\Setting::where('name->en', 'Service Provider')->first();
            $coins = $setting ? (int) $setting->value : 15;
            $service->user->increment('coins', $coins);
        }
    });
}

public function getImagesAttribute($value)
{
    if (is_null($value)) {
        return [];
    }

    $images = is_array($value) ? $value : json_decode($value, true);

    return collect($images)
        ->filter()
        ->map(function ($path) {

            if (str_starts_with($path, 'http')) {
                return $path;
            }

           
            return asset('storage/' . $path);
        })
        ->values()
        ->toArray();
}

}

