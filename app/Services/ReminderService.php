<?php

namespace App\Services;

use App\Models\Reminder;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ReminderService
{
    public function getAllReminders(User $user, array $filters = [])
    {
        $query = Reminder::where('user_id', $user->id);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->where('date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->where('date', '<=', $filters['to_date']);
        }

        return $query->orderBy('date')->orderBy('time')->get();
    }

    public function getActiveCount(User $user): int
    {
        return Reminder::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();
    }

    public function createReminder(User $user, array $data): Reminder
    {
        $attachmentPath = null;
        if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
            $attachmentPath = $data['attachment']->store('reminders/' . $user->id, 'public');
        }

        return Reminder::create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'date' => $data['date'],
            'time' => $data['time'],
            'timezone' => $data['timezone'] ?? 'UTC',
            'recurrence' => $data['recurrence'],
            'notes' => $data['notes'] ?? null,
            'attachment' => $attachmentPath,
            'status' => 'active',
        ]);
    }

    public function updateReminder(Reminder $reminder, array $data): Reminder
    {
        $updateData = [];

        $fields = ['title', 'date', 'time', 'recurrence', 'status', 'timezone', 'notes'];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
            if ($reminder->attachment) {
                Storage::disk('public')->delete($reminder->attachment);
            }
            $updateData['attachment'] = $data['attachment']->store('reminders/' . $reminder->user_id, 'public');
        }

        if (!empty($updateData)) {
            $reminder->update($updateData);
        }

        return $reminder->fresh();
    }

    public function deleteReminder(Reminder $reminder): void
    {
        if ($reminder->attachment) {
            Storage::disk('public')->delete($reminder->attachment);
        }
        $reminder->delete();
    }

    public function findReminderForUser(User $user, int $reminderId): ?Reminder
    {
        return Reminder::where('user_id', $user->id)->find($reminderId);
    }
}

