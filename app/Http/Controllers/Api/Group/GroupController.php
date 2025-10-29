<?php

namespace App\Http\Controllers\Api\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Requests\JoinGroupRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\SendSosAlertRequest;
use App\Http\Resources\GroupResource;
use App\Http\Resources\GroupMemberResource;
use App\Http\Resources\GroupLocationResource;
use App\Http\Resources\GroupSosAlertResource;
use App\Models\Group;
use App\Models\GroupMember;
use App\Services\GroupService;
use App\Utils\ApiResponse;
use App\Helpers\LangHelper;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * Get all user's groups
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            $groups = Group::whereHas('members', function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'active');
            })
            ->with([
                'owner',
                'groupMembers' => function($query) {
                    $query->where('status', 'active')->with('user');
                },
                'activeSosAlerts.user'
            ])
            ->withCount([
                'members as active_members_count' => function($query) {
                    $query->where('status', 'active');
                },
                'outOfRangeMembers as out_of_range_count'
            ])
            ->get();

            return ApiResponse::success(LangHelper::msg('groups_fetched'), GroupResource::collection($groups));
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('groups_fetch_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Create new group
     */
    public function store(CreateGroupRequest $request)
    {
        try {
            $group = $this->groupService->createGroup($request->validated(), $request->user()->id);

            $group->load([
                'owner',
                'groupMembers' => function($query) {
                    $query->where('status', 'active')->with('user');
                }
            ]);

            return ApiResponse::created(LangHelper::msg('group_created'), new GroupResource($group));
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('group_create_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get single group details
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();

            // 🔹 تحميل الجروب بكل العلاقات المطلوبة
            $group = Group::with([
                'owner',
                'groupMembers' => function ($query) {
                    $query->where('status', 'active')
                        ->with('user');
                },
                'activeSosAlerts.user'
            ])
            ->withCount([
                'members as active_members_count' => function ($query) {
                    $query->where('status', 'active');
                },
                'outOfRangeMembers as out_of_range_count'
            ])
            ->findOrFail($id);

            // 🔹 التأكد أن المستخدم عضو فعّال في الجروب
            $isMember = $group->members()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();

            if (!$isMember) {
                return ApiResponse::forbidden(LangHelper::msg('group_no_permission'));
            }

            // 🔹 تحميل آخر موقع لكل عضو في المجموعة
            $group->groupMembers->load(['locations' => function ($query) use ($id) {
                $query->where('group_id', $id)
                    ->latest('updated_at')
                    ->limit(1);
            }]);

            // ✅ الآن كل عضو يحتوي على latest_location داخل GroupMemberResource
            return ApiResponse::success(
                LangHelper::msg('group_fetched'),
                new GroupResource($group)
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                LangHelper::msg('group_fetch_failed') . ': ' . $e->getMessage()
            );
        }
    }


    /**
     * Update group
     */
    public function update(UpdateGroupRequest $request, $id)
    {
        try {
            $user = $request->user();

            $group = Group::where('id', $id)
                ->where('owner_id', $user->id)
                ->firstOrFail();

            $group->update($request->validated());
            $group->load([
                'owner',
                'groupMembers' => function($query) {
                    $query->where('status', 'active')->with('user');
                }
            ]);

            return ApiResponse::success(LangHelper::msg('group_updated'), new GroupResource($group));
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('group_update_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Delete group
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();

            $group = Group::where('id', $id)
                ->where('owner_id', $user->id)
                ->firstOrFail();

            $group->delete();

            return ApiResponse::success(LangHelper::msg('group_deleted'));
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('group_delete_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Join group using invite code
     */
    public function join(JoinGroupRequest $request)
    {
        try {
            $group = $this->groupService->joinGroup(
                $request->invite_code,
                $request->user()->id
            );

            $group->load([
                'owner',
                'groupMembers' => function($query) {
                    $query->where('status', 'active')->with('user');
                }
            ]);

            return ApiResponse::success(LangHelper::msg('group_joined'), new GroupResource($group));
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('group_join_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Leave group
     */
    public function leave(Request $request, $id)
    {
        try {
            $this->groupService->leaveGroup($id, $request->user()->id);

            return ApiResponse::success(LangHelper::msg('group_left'));
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('group_leave_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Remove member from group (owner only)
     */
    public function removeMember(Request $request, $groupId, $userId)
    {
        try {
            $this->groupService->removeMember($groupId, $request->user()->id, $userId);

            return ApiResponse::success(LangHelper::msg('group_member_removed'));
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('group_member_remove_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Update member location
     */
    public function updateLocation(UpdateLocationRequest $request, $id)
    {
        try {
            $location = $this->groupService->updateLocation(
                $id,
                $request->user()->id,
                $request->latitude,
                $request->longitude
            );

            return ApiResponse::success(
                LangHelper::msg('location_updated'),
                new GroupLocationResource($location)
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('location_update_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get group members with their locations
     */
    public function members(Request $request, $id)
    {
        try {
            $user = $request->user();

            $group = Group::findOrFail($id);

            // Check if user is a member
            $isMember = $group->members()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();

            if (!$isMember) {
                return ApiResponse::forbidden(LangHelper::msg('group_members_no_permission'));
            }

            $members = GroupMember::where('group_id', $id)
                ->where('status', 'active')
                ->with([
                    'user',
                    'locations' => function ($query) use ($id) {
                        $query->where('group_id', $id)
                            ->latest('updated_at')
                            ->limit(1);
                    }
                ])
                ->get();

            return ApiResponse::success(
                LangHelper::msg('group_members_fetched'),
                GroupMemberResource::collection($members)
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('group_members_fetch_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Send SOS alert
     */
    public function sendSos(SendSosAlertRequest $request, $id)
    {
        try {
            $alert = $this->groupService->sendSosAlert(
                $id,
                $request->user()->id,
                $request->latitude,
                $request->longitude,
                $request->message
            );

            $alert->load('user');

            return ApiResponse::success(
                LangHelper::msg('sos_sent'),
                new GroupSosAlertResource($alert)
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('sos_send_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Resolve SOS alert
     */
    public function resolveSos(Request $request, $groupId, $alertId)
    {
        try {
            $user = $request->user();

            $group = Group::findOrFail($groupId);

            // Check if user is a member
            $isMember = $group->members()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();

            if (!$isMember) {
                return ApiResponse::forbidden(LangHelper::msg('sos_no_permission'));
            }

            $alert = $group->sosAlerts()->findOrFail($alertId);
            $alert->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);

            return ApiResponse::success(
                LangHelper::msg('sos_resolved'),
                new GroupSosAlertResource($alert)
            );
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('sos_resolve_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Get group invite details (for QR/code scan)
     */
    public function inviteDetails($inviteCode)
    {
        try {
            $group = Group::where('invite_code', $inviteCode)
                ->where('is_active', true)
                ->with(['owner'])
                ->withCount([
                    'members as active_members_count' => function($query) {
                        $query->where('status', 'active');
                    }
                ])
                ->firstOrFail();

            return ApiResponse::success(LangHelper::msg('group_invite_fetched'), [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'owner' => $group->owner->name,
                'members_count' => $group->active_members_count,
                'invite_code' => $group->invite_code,
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(LangHelper::msg('group_invite_invalid'));
        }
    }
}

