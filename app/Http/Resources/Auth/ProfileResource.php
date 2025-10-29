<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'photo_url' => $this->photo_url
                ? (str_starts_with($this->photo_url, 'http')
                    ? $this->photo_url
                    : asset('storage/' . $this->photo_url))
                : null,
            // 'address' => $this->address,
            'city' => $this->city,
            // 'language' => $this->language,
            'currency' => $this->currency,
            'coins' => $this->coins,
            'trips' => $this->trips,
        ];
    }
}
