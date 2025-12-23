<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof \App\Models\User) {
            // Owner is always within radius and distance is always 0
            $isOwner = $this->pivot->role === 'owner';
            $isWithinRadius = $isOwner ? true : $this->pivot->is_within_radius;
            
            return [
                'id' => $this->id,
                'name' => $this->name,
                'nickname' => $this->nickname,
                'email' => $this->email,
                'phone' => $this->phone,
                'avatar' => $this->avatar,
                'is_online' => $this->is_online,
                'user_status' => $this->status,
                'last_seen' => $this->last_seen,
                'role' => $this->pivot->role,
                'status' => $this->pivot->status,
                'is_within_radius' => $isWithinRadius,
                'out_of_range_count' => $isOwner ? 0 : $this->pivot->out_of_range_count,
                'joined_at' => $this->pivot->joined_at,
                'last_location_update' => $this->pivot->last_location_update,
                'latest_location' => null,
            ];
        }

        // From GroupMember model directly
        $user = $this->relationLoaded('user') ? $this->user : null;

        // Get latest location from loaded locations
        $location = null;
        if ($this->relationLoaded('locations') && $this->locations->isNotEmpty()) {
            $location = $this->locations->first();
        }

        // Owner is always within radius and distance is always 0
        $isOwner = $this->role === 'owner';
        $isWithinRadius = $isOwner ? true : $this->is_within_radius;
        $distanceFromCenter = $isOwner ? 0 : ($location ? (float) ($location->distance_from_center ?? 0) : 0);

        return [
            'id' => $user?->id ?? $this->user_id,
            'name' => $user?->name,
            'nickname' => $user?->nickname,
            'email' => $user?->email,
            'phone' => $user?->phone,
            'avatar' => $user?->avatar,
            'is_online' => $user?->is_online,
            'user_status' => $user?->status,
            'last_seen' => $user?->last_seen,
            'role' => $this->role,
            'status' => $this->status,
            'is_within_radius' => $isWithinRadius,
            'out_of_range_count' => $isOwner ? 0 : $this->out_of_range_count,
            'joined_at' => $this->joined_at?->toIso8601String(),
            'last_location_update' => $this->last_location_update?->toIso8601String(),
            'latest_location' => $location ? [
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'distance_from_center' => $distanceFromCenter,
            ] : null,
        ];
    }
}
