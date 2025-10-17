<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $nameParts = explode(' ', $this->name, 2);

        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'first_name' => $nameParts[0] ?? null,
            'last_name' => $nameParts[1] ?? null,
            'address' => $this->address,
            'avatar' => $this->avatar,
            'provider' => $this->provider,
            'provider_id' => $this->provider_id,
            'role' => $this->role,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'request' => $this->request,
            'coins' => $this->coins,
            'trips' => $this->trips,
            'has_interests' => (bool) $this->has_interests,
        ];
    }
}
