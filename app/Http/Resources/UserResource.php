<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Format photo_url - if it's a full URL, use it as is, otherwise prepend storage path
        $photoUrl = null;
        if ($this->photo_url) {
            $photoUrl = str_starts_with($this->photo_url, 'http') 
                ? $this->photo_url 
                : asset('storage/' . $this->photo_url);
        }
        
        // Use photo_url if avatar is null/empty, otherwise use avatar
        $avatarValue = $this->avatar ?: $photoUrl;
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $avatarValue,
            'photo_url' => $photoUrl,
            'is_online' => $this->is_online,
            'status' => $this->status,
            'last_seen' => $this->last_seen,
            'last_active_at' => $this->last_active_at?->toIso8601String(),
        ];
    }
}
