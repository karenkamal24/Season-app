<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use App\Models\User;

class NotificationController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Send notification to specific user
     */
    public function sendToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
            'data' => 'sometimes|array'
        ]);

        $user = User::find($request->user_id);

        if (!$user->fcm_token) {
            return response()->json([
                'success' => false,
                'message' => 'User has no FCM token'
            ], 400);
        }

        try {
            $response = $this->firebaseService->sendToDevice(
                $user->fcm_token,
                $request->title,
                $request->body,
                $request->data ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully',
                'response' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to all users
     */
    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'data' => 'sometimes|array'
        ]);

        try {
            $response = $this->firebaseService->sendToAllUsers(
                $request->title,
                $request->body,
                $request->data ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Notification sent to all users',
                'response' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to multiple specific users
     */
    public function sendToMultiple(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
            'data' => 'sometimes|array'
        ]);

        $users = User::whereIn('id', $request->user_ids)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        if (empty($users)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid FCM tokens found'
            ], 400);
        }

        try {
            $responses = $this->firebaseService->sendToMultipleDevices(
                $users,
                $request->title,
                $request->body,
                $request->data ?? []
            );

            $successCount = collect($responses)->where('success', true)->count();
            $failureCount = collect($responses)->where('success', false)->count();

            return response()->json([
                'success' => true,
                'message' => "Sent to {$successCount} users, {$failureCount} failed",
                'details' => $responses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

