<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['name', 'value', 'max'];

    protected $casts = [
        'name' => 'array',
    ];


    public function getLocalizedNameAttribute(): string
{
    if (is_array($this->name)) {
        return $this->name[app()->getLocale()] ?? $this->name['en'] ?? reset($this->name);
    }

    return (string) $this->name;
}

}
