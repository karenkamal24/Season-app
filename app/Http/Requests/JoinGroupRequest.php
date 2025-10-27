<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invite_code' => 'required|string|exists:groups,invite_code',
        ];
    }

    public function messages(): array
    {
        return [
            'invite_code.required' => 'كود الدعوة مطلوب',
            'invite_code.exists' => 'كود الدعوة غير صحيح',
        ];
    }
}

