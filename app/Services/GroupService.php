<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupLocation;
use App\Models\GroupSosAlert;
use App\Models\User;
use App\Helpers\LangHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GroupService
{
    /**
     * Generate unique invite code
     */
    public function generateInviteCode()
    {
        do {
            $code = 'SEASON-' . strtoupper(Str::random(6));
        } while (Group::where('invite_code', $code)->exists());

        return $code;
    }

    /**
     * Generate QR code for group invitation
     */
    public function generateQRCode($inviteCode)
    {
        try {
            // Use SVG format instead of PNG (doesn't require imagick)
            $qrCode = QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($inviteCode);

            return base64_encode($qrCode);
        } catch (\Exception $e) {
            // If QR generation fails, return null (invite code still works)
            Log::warning('Failed to generate QR code: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new group
     */
    public function createGroup($data, $userId)
    {
        $inviteCode = $this->generateInviteCode();

        $group = Group::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'owner_id' => $userId,
            'invite_code' => $inviteCode,
            'qr_code' => $this->generateQRCode($inviteCode),
            'safety_radius' => $data['safety_radius'] ?? 100,
            'notifications_enabled' => $data['notifications_enabled'] ?? true,
        ]);

        // Add owner as member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $userId,
            'role' => 'owner',
            'status' => 'active',
        ]);

        return $group;
    }

    /**
     * Join group using invite code
     */
    public function joinGroup($inviteCode, $userId)
    {
        $group = Group::where('invite_code', $inviteCode)
            ->where('is_active', true)
            ->firstOrFail();

        // Check if already a member
        $existing = GroupMember::where('group_id', $group->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if ($existing->status === 'active') {
                throw new \Exception(LangHelper::msg('group_already_member'));
            }
            // Rejoin if previously left
            $existing->update(['status' => 'active', 'joined_at' => now()]);
            return $group;
        }

        // Add as new member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $userId,
            'role' => 'member',
            'status' => 'active',
        ]);

        return $group;
    }

    /**
     * Update member location
     */
    public function updateLocation($groupId, $userId, $latitude, $longitude)
    {
        $group = Group::findOrFail($groupId);
        $member = GroupMember::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->firstOrFail();

        // Calculate distance from group center (owner's location)
        $centerLocation = $this->getGroupCenter($groupId);

        if ($centerLocation) {
            $distance = $this->calculateDistance(
                $centerLocation['latitude'],
                $centerLocation['longitude'],
                $latitude,
                $longitude
            );
        } else {
            $distance = 0; // First location becomes center
        }

        $isWithinRadius = $distance <= $group->safety_radius;

        // Create location record
        $location = GroupLocation::create([
            'group_id' => $groupId,
            'user_id' => $userId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance_from_center' => $distance,
            'is_within_radius' => $isWithinRadius,
        ]);

        // Update member status
        $wasOutOfRange = !$member->is_within_radius;
        $updateData = [
            'is_within_radius' => $isWithinRadius,
            'out_of_range_count' => !$isWithinRadius ? $member->out_of_range_count + 1 : $member->out_of_range_count,
            'last_location_update' => now(),
        ];

        // Handle notifications based on status
        if (!$isWithinRadius && $group->notifications_enabled) {
            // Member is OUT OF RANGE
            $shouldSendNotification = false;
            
            // Check if we should send notification (first time or 2 minutes passed)
            if ($member->last_notification_sent_at === null) {
                // First time out of range
                $shouldSendNotification = true;
            } else {
                // Check if 2 minutes have passed since last notification
                $minutesSinceLastNotification = now()->diffInMinutes($member->last_notification_sent_at);
                if ($minutesSinceLastNotification >= 2) {
                    $shouldSendNotification = true;
                }
            }
            
            if ($shouldSendNotification) {
                $this->sendOutOfRangeNotification($group, $member->user, $distance);
                $updateData['last_notification_sent_at'] = now();
            }
        } else if ($isWithinRadius && $member->last_notification_sent_at !== null) {
            // Member is BACK IN RANGE - stop notifications
            $updateData['last_notification_sent_at'] = null;
        }

        $member->update($updateData);

        return $location;
    }

    /**
     * Get group center point (owner's location)
     */
    protected function getGroupCenter($groupId)
    {
        $group = Group::findOrFail($groupId);

        // Get the owner's latest location
        $ownerLocation = GroupLocation::where('group_id', $groupId)
            ->where('user_id', $group->owner_id)
            ->latest('updated_at')
            ->first();

        if (!$ownerLocation) {
            return null;
        }

        return [
            'latitude' => $ownerLocation->latitude,
            'longitude' => $ownerLocation->longitude,
        ];
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Send out of range notification
     */
    protected function sendOutOfRangeNotification($group, $user, $distance)
    {

        $members = GroupMember::where('group_id', $group->id)
            ->where('user_id', '!=', $user->id)
            ->where('status', 'active')
            ->with('user')
            ->get();

        $firebaseService = app(FirebaseService::class);

        foreach ($members as $member) {
            if ($member->user->fcm_token) {
                try {
                    $firebaseService->sendToDevice(
                        $member->user->fcm_token,
                        'تنبيه: عضو خارج النطاق',
                        "{$user->name} خارج النطاق - المسافة: {$distance}متر (النطاق الآمن: {$group->safety_radius}متر)",
                        [
                            'type' => 'out_of_range',
                            'group_id' => (string) $group->id,
                            'user_id' => (string) $user->id,
                            'user_name' => $user->name,
                            'distance' => (string) $distance,
                            'safety_radius' => (string) $group->safety_radius,
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send out of range notification: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Send SOS alert
     */
    public function sendSosAlert($groupId, $userId, $latitude, $longitude, $message = null)
    {
        $group = Group::findOrFail($groupId);
        $user = User::findOrFail($userId);

        // Create SOS alert
        $alert = GroupSosAlert::create([
            'group_id' => $groupId,
            'user_id' => $userId,
            'message' => $message ?? 'إشارة SOS - طوارئ',
            'latitude' => $latitude,
            'longitude' => $longitude,
            'status' => 'active',
        ]);

        // Send notifications to all group members
        $members = GroupMember::where('group_id', $groupId)
            ->where('user_id', '!=', $userId)
            ->where('status', 'active')
            ->with('user')
            ->get();

        $firebaseService = app(FirebaseService::class);

        foreach ($members as $member) {
            if ($member->user->fcm_token) {
                try {
                    $firebaseService->sendToDevice(
                        $member->user->fcm_token,
                        '🚨 إشارة SOS - طوارئ',
                        "{$user->name} يحتاج المساعدة! {$message}",
                        [
                            'type' => 'sos_alert',
                            'group_id' => (string) $group->id,
                            'alert_id' => (string) $alert->id,
                            'user_id' => (string) $user->id,
                            'latitude' => (string) $latitude,
                            'longitude' => (string) $longitude,
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Failed to send SOS notification: ' . $e->getMessage());
                }
            }
        }

        return $alert;
    }

    /**
     * Leave group
     */
    public function leaveGroup($groupId, $userId)
    {
        $member = GroupMember::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->firstOrFail();

        if ($member->role === 'owner') {
            throw new \Exception(LangHelper::msg('group_owner_cannot_leave'));
        }

        $member->update(['status' => 'left']);

        return true;
    }

    /**
     * Remove member from group
     */
    public function removeMember($groupId, $ownerId, $memberUserId)
    {
        $group = Group::where('id', $groupId)
            ->where('owner_id', $ownerId)
            ->firstOrFail();

        $member = GroupMember::where('group_id', $groupId)
            ->where('user_id', $memberUserId)
            ->where('status', 'active')
            ->firstOrFail();

        if ($member->role === 'owner') {
            throw new \Exception(LangHelper::msg('group_cannot_remove_owner'));
        }

        $member->update(['status' => 'removed']);

        return true;
    }
}

