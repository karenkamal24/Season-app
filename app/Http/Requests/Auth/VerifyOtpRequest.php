<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\LangHelper;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'otp'   => ['required', 'digits:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => LangHelper::msg('email_required'),
            'email.email'    => LangHelper::msg('email_invalid'),
            'email.exists'   => LangHelper::msg('email_not_found'),
            'otp.required'   => LangHelper::msg('otp_required'),
            'otp.digits'     => LangHelper::msg('otp_digits'),
        ];
    }
}
