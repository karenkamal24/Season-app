<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'owner_id' => $this->owner_id,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'invite_code' => $this->invite_code,
            'qr_code' => $this->qr_code,
            'safety_radius' => $this->safety_radius,
            'notifications_enabled' => $this->notifications_enabled,
            'is_active' => $this->is_active,
            'members_count' => $this->active_members_count ?? $this->when(
                $this->relationLoaded('groupMembers'),
                fn() => $this->groupMembers->where('status', 'active')->count()
            ),
            'out_of_range_count' => $this->out_of_range_count ?? $this->when(
                $this->relationLoaded('groupMembers'),
                fn() => $this->groupMembers->where('status', 'active')
                    ->where('role', '!=', 'owner') // Owner is never out of range
                    ->where('is_within_radius', false)
                    ->count()
            ),
            'members' => GroupMemberResource::collection($this->whenLoaded('groupMembers')),
            'active_sos_alerts' => GroupSosAlertResource::collection($this->whenLoaded('activeSosAlerts')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}

