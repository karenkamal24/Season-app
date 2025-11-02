<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReminderResource extends JsonResource
{
    protected function getRecurrenceTranslation($recurrence, $lang)
    {
        $translations = [
            'once' => [
                'en' => 'Once',
                'ar' => 'مرة واحدة'
            ],
            'daily' => [
                'en' => 'Daily',
                'ar' => 'يومي'
            ],
            'weekly' => [
                'en' => 'Weekly',
                'ar' => 'أسبوعي'
            ],
            'monthly' => [
                'en' => 'Monthly',
                'ar' => 'شهري'
            ],
        ];

        return $translations[$recurrence][$lang] ?? $recurrence;
    }

    public function toArray(Request $request): array
    {
        $lang = strtolower($request->header('Accept-Language', 'ar'));
        $lang = in_array($lang, ['ar', 'en']) ? $lang : 'ar';

        $timeValue = $this->time instanceof \Carbon\Carbon
            ? $this->time->format('H:i:s')
            : $this->time;

        return [
            'reminder_id' => $this->id,
            'title' => $this->title,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'time' => $timeValue,
            'timezone' => $this->timezone ?? 'UTC',
            'recurrence' => $this->getRecurrenceTranslation($this->recurrence, $lang),
            'notes' => $this->notes,
            'attachment' => $this->attachment ? asset('storage/' . $this->attachment) : null,
            'status' => $this->status ?? 'active',
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
