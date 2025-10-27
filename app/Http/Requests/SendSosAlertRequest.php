<?php

namespace App\Http\Requests;

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
            'latitude.required' => 'خط العرض مطلوب',
            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و 90',
            'longitude.required' => 'خط الطول مطلوب',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و 180',
            'message.max' => 'الرسالة يجب أن لا تتجاوز 500 حرف',
        ];
    }
}

