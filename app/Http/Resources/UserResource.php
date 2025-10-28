<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar,
            'photo_url' => $this->photo_url ? asset('storage/' . $this->photo_url) : null,
            'is_online' => $this->is_online,
            'status' => $this->status,
            'last_seen' => $this->last_seen,
            'last_active_at' => $this->last_active_at?->toIso8601String(),
        ];
    }
}
