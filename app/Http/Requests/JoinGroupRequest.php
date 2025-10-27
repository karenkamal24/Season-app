<?php

namespace App\Http\Requests;

use App\Helpers\LangHelper;
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
            'invite_code.required' => LangHelper::msg('group_invite_code_required'),
            'invite_code.exists' => LangHelper::msg('group_invite_invalid'),
        ];
    }
}

