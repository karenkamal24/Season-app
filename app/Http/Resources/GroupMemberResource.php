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
                'role' => $this->pivot->role,
                'status' => $this->pivot->status,
                'is_within_radius' => $this->pivot->is_within_radius,
                'out_of_range_count' => $this->pivot->out_of_range_count,
                'joined_at' => $this->pivot->joined_at,
                'last_location_update' => $this->pivot->last_location_update,
                'latest_location' => new GroupLocationResource($this->whenLoaded('latestLocation')),
            ];
        }

        // From GroupMember model directly
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'role' => $this->role,
            'status' => $this->status,
            'is_within_radius' => $this->is_within_radius,
            'out_of_range_count' => $this->out_of_range_count,
            'joined_at' => $this->joined_at?->toIso8601String(),
            'last_location_update' => $this->last_location_update?->toIso8601String(),
            'latest_location' => $this->when(
                $this->relationLoaded('locations') && $this->locations->isNotEmpty(),
                fn() => new GroupLocationResource($this->locations->first())
            ),
        ];
    }
}

