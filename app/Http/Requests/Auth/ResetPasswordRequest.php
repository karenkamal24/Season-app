<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\LangHelper;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex'      => LangHelper::msg('password_regex_invalid'),
            'password.confirmed'  => LangHelper::msg('password_confirm_mismatch'),
        ];
    }
}
