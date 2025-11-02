<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:200',
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'required|string',
            'recurrence' => 'required|in:once,daily,weekly,monthly',
            'notes' => 'nullable|string|max:1000',
            'timezone' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'title' => 'nullable|string|max:200',
                'date' => 'nullable|date|date_format:Y-m-d',
                'time' => 'nullable|string',
                'recurrence' => 'nullable|in:once,daily,weekly,monthly',
                'notes' => 'nullable|string|max:1000',
                'status' => 'nullable|in:active,completed,cancelled',
                'timezone' => 'nullable|string',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:10240',
            ];
        }

        return $rules;
    }

    protected function prepareForValidation()
    {
        if ($this->has('time')) {
            $time = trim($this->input('time', ''));
            if ($time !== '' && $time !== null) {
                if (is_numeric($time) && strlen($time) <= 2) {
                    $time = str_pad($time, 2, '0', STR_PAD_LEFT) . ':00';
                } elseif (strpos($time, ':') === false && is_numeric($time)) {
                    $time = str_pad($time, 2, '0', STR_PAD_LEFT) . ':00';
                } elseif (preg_match('/^\d{1,2}:\d{1,2}$/', $time)) {
                    [$hour, $minute] = explode(':', $time);
                    $time = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT);
                }
                $this->merge(['time' => $time]);
            }
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'Validation failed',
                'details' => collect($validator->errors()->toArray())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'message' => $messages[0]
                    ];
                })->values()
            ]
        ], 400));
    }
}

