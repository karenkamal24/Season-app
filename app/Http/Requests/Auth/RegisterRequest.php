<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\LangHelper;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s]+$/u'],
            'last_name'  => ['required', 'string', 'max:100', 'regex:/^[\p{L}\s]+$/u'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => [
                'nullable',
                'phone:ZZ',
                'unique:users,phone',
                'regex:/^\+[1-9]\d{6,14}$/',
            ],
            'password' => [
                'required','string','min:8','confirmed',
                'regex:/[a-z]/','regex:/[A-Z]/','regex:/[0-9]/','regex:/[@$!%*?&]/'
            ],
            'notification_token' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => LangHelper::msg('first_name_required'),
            'last_name.required'  => LangHelper::msg('last_name_required'),
            'first_name.regex'    => LangHelper::msg('first_name_regex'),
            'last_name.regex'     => LangHelper::msg('last_name_regex'),

            'phone.phone'         => LangHelper::msg('phone_invalid'),
            'phone.regex'         => LangHelper::msg('phone_regex'),
            'phone.unique'        => LangHelper::msg('phone_unique'),

            'password.regex'      => LangHelper::msg('password_regex_invalid'),
            'password.confirmed'  => LangHelper::msg('password_confirm_mismatch'),
        ];
    }
}
