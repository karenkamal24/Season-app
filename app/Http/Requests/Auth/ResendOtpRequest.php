<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\LangHelper;

class ResendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => LangHelper::msg('email_required'),
            'email.email'    => LangHelper::msg('email_invalid'),
            'email.exists'   => LangHelper::msg('email_not_found'),
        ];
    }
}
