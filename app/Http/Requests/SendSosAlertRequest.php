<?php

namespace App\Http\Requests;

use App\Helpers\LangHelper;
use Illuminate\Foundation\Http\FormRequest;

class SendSosAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'message' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.required' => LangHelper::msg('latitude_required'),
            'latitude.between' => LangHelper::msg('latitude_invalid'),
            'longitude.required' => LangHelper::msg('longitude_required'),
            'longitude.between' => LangHelper::msg('longitude_invalid'),
            'message.max' => LangHelper::msg('sos_message_max'),
        ];
    }
}

