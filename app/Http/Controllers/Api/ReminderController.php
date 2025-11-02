<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReminderResource;
use App\Http\Requests\ReminderRequest;
use App\Services\ReminderService;
use App\Helpers\LangHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    protected $reminderService;

    public function __construct(ReminderService $reminderService)
    {
        $this->reminderService = $reminderService;
    }
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $filters = $request->only(['status', 'from_date', 'to_date']);

            $reminders = $this->reminderService->getAllReminders($user, $filters);
            $activeCount = $this->reminderService->getActiveCount($user);

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('reminders_fetched'),
                'data' => [
                    'reminders' => ReminderResource::collection($reminders),
                    'active_count' => $activeCount,
                    'total_count' => $reminders->count(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('reminder_fetch_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(ReminderRequest $request)
    {
        try {
            $user = Auth::user();
            $data = $request->validated();

            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment');
            }

            $reminder = $this->reminderService->createReminder($user, $data);

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('reminder_created'),
                'data' => new ReminderResource($reminder)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('reminder_create_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();
            $reminder = $this->reminderService->findReminderForUser($user, $id);

            if (!$reminder) {
                return response()->json([
                    'success' => false,
                    'message' => LangHelper::msg('reminder_not_found')
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('reminder_fetched'),
                'data' => new ReminderResource($reminder)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('reminder_fetch_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(ReminderRequest $request, $id)
    {
        try {
            $user = Auth::user();
            $reminder = $this->reminderService->findReminderForUser($user, $id);

            if (!$reminder) {
                return response()->json([
                    'success' => false,
                    'message' => LangHelper::msg('reminder_not_found')
                ], 404);
            }

            $data = $request->validated();

            if ($request->hasFile('attachment')) {
                $data['attachment'] = $request->file('attachment');
            }

            $updatedReminder = $this->reminderService->updateReminder($reminder, $data);

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('reminder_updated'),
                'data' => new ReminderResource($updatedReminder)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('reminder_update_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $reminder = $this->reminderService->findReminderForUser($user, $id);

            if (!$reminder) {
                return response()->json([
                    'success' => false,
                    'message' => LangHelper::msg('reminder_not_found')
                ], 404);
            }

            $this->reminderService->deleteReminder($reminder);

            return response()->json([
                'success' => true,
                'message' => LangHelper::msg('reminder_deleted')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => LangHelper::msg('reminder_delete_failed'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

