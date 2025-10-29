<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Check if this is from pivot (many-to-many) or direct model
        if ($this->resource instanceof \App\Models\User) {
            // From pivot relationship
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
                'is_within_radius' => $this->pivot->is_within_radius,
                'out_of_range_count' => $this->pivot->out_of_range_count,
                'joined_at' => $this->pivot->joined_at,
                'last_location_update' => $this->pivot->last_location_update,
                'latest_location' => $this->when(
                    $this->relationLoaded('latestLocation') && $this->latestLocation,
                    function() {
                        $location = $this->latestLocation;
                        return [
                            'latitude' => $location->latitude,
                            'longitude' => $location->longitude,
                            'distance_from_center' => $location->distance_from_center,
                        ];
                    }
                ),
            ];
        }

        // From GroupMember model directly
        $user = $this->relationLoaded('user') ? $this->user : null;

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
            'is_within_radius' => $this->is_within_radius,
            'out_of_range_count' => $this->out_of_range_count,
            'joined_at' => $this->joined_at?->toIso8601String(),
            'last_location_update' => $this->last_location_update?->toIso8601String(),
            'latest_location' => $this->when(
                $this->relationLoaded('locations') && $this->locations->isNotEmpty(),
                function() {
                    $location = $this->locations->first();
                    return [
                        'latitude' => $location->latitude,
                        'longitude' => $location->longitude,
                        'distance_from_center' => $location->distance_from_center,
                    ];
                }
            ),
        ];
    }
}

