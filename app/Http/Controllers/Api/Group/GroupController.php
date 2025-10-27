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
                'members' => function($query) {
                    $query->where('group_members.status', 'active');
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

            return ApiResponse::success('تم جلب المجموعات بنجاح', GroupResource::collection($groups));
        } catch (\Exception $e) {
            return ApiResponse::error('فشل جلب المجموعات: ' . $e->getMessage());
        }
    }

    /**
     * Create new group
     */
    public function store(CreateGroupRequest $request)
    {
        try {
            $group = $this->groupService->createGroup($request->validated(), $request->user()->id);

            $group->load(['owner', 'activeMembers']);

            return ApiResponse::created('تم إنشاء المجموعة بنجاح', new GroupResource($group));
        } catch (\Exception $e) {
            return ApiResponse::error('فشل إنشاء المجموعة: ' . $e->getMessage());
        }
    }

    /**
     * Get single group details
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();

            $group = Group::with([
                'owner',
                'activeMembers.latestLocation',
                'activeSosAlerts.user'
            ])
            ->withCount([
                'members as active_members_count' => function($query) {
                    $query->where('status', 'active');
                },
                'outOfRangeMembers as out_of_range_count'
            ])
            ->findOrFail($id);

            // Check if user is a member
            $isMember = $group->members()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();

            if (!$isMember) {
                return ApiResponse::forbidden('ليس لديك صلاحية لعرض هذه المجموعة');
            }

            return ApiResponse::success('تم جلب بيانات المجموعة بنجاح', new GroupResource($group));
        } catch (\Exception $e) {
            return ApiResponse::error('فشل جلب بيانات المجموعة: ' . $e->getMessage());
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
            $group->load(['owner', 'activeMembers']);

            return ApiResponse::success('تم تحديث المجموعة بنجاح', new GroupResource($group));
        } catch (\Exception $e) {
            return ApiResponse::error('فشل تحديث المجموعة: ' . $e->getMessage());
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

            return ApiResponse::success('تم حذف المجموعة بنجاح');
        } catch (\Exception $e) {
            return ApiResponse::error('فشل حذف المجموعة: ' . $e->getMessage());
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

            $group->load(['owner', 'activeMembers']);

            return ApiResponse::success('تم الانضمام للمجموعة بنجاح', new GroupResource($group));
        } catch (\Exception $e) {
            return ApiResponse::error('فشل الانضمام للمجموعة: ' . $e->getMessage());
        }
    }

    /**
     * Leave group
     */
    public function leave(Request $request, $id)
    {
        try {
            $this->groupService->leaveGroup($id, $request->user()->id);

            return ApiResponse::success('تم مغادرة المجموعة بنجاح');
        } catch (\Exception $e) {
            return ApiResponse::error('فشل مغادرة المجموعة: ' . $e->getMessage());
        }
    }

    /**
     * Remove member from group (owner only)
     */
    public function removeMember(Request $request, $groupId, $userId)
    {
        try {
            $this->groupService->removeMember($groupId, $request->user()->id, $userId);

            return ApiResponse::success('تم إزالة العضو من المجموعة بنجاح');
        } catch (\Exception $e) {
            return ApiResponse::error('فشل إزالة العضو: ' . $e->getMessage());
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
                'تم تحديث الموقع بنجاح',
                new GroupLocationResource($location)
            );
        } catch (\Exception $e) {
            return ApiResponse::error('فشل تحديث الموقع: ' . $e->getMessage());
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
                return ApiResponse::forbidden('ليس لديك صلاحية لعرض أعضاء هذه المجموعة');
            }

            $members = GroupMember::where('group_id', $id)
                ->where('status', 'active')
                ->with(['user', 'latestLocation'])
                ->get();

            return ApiResponse::success(
                'تم جلب أعضاء المجموعة بنجاح',
                GroupMemberResource::collection($members)
            );
        } catch (\Exception $e) {
            return ApiResponse::error('فشل جلب أعضاء المجموعة: ' . $e->getMessage());
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
                'تم إرسال إشارة SOS بنجاح',
                new GroupSosAlertResource($alert)
            );
        } catch (\Exception $e) {
            return ApiResponse::error('فشل إرسال إشارة SOS: ' . $e->getMessage());
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
                return ApiResponse::forbidden('ليس لديك صلاحية');
            }

            $alert = $group->sosAlerts()->findOrFail($alertId);
            $alert->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);

            return ApiResponse::success(
                'تم إغلاق إشارة SOS',
                new GroupSosAlertResource($alert)
            );
        } catch (\Exception $e) {
            return ApiResponse::error('فشل إغلاق إشارة SOS: ' . $e->getMessage());
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

            return ApiResponse::success('تم جلب معلومات الدعوة بنجاح', [
                'id' => $group->id,
                'name' => $group->name,
                'description' => $group->description,
                'owner' => $group->owner->name,
                'members_count' => $group->active_members_count,
                'invite_code' => $group->invite_code,
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error('كود الدعوة غير صحيح');
        }
    }
}

