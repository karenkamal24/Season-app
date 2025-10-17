<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

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
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
            'first_name.regex'    => 'First name must contain only letters.',
            'last_name.regex'     => 'Last name must contain only letters.',
            'phone.phone'         => 'Please enter a valid international phone number.',
            'phone.regex'         => 'Phone number must start with + and include country code.',
            'phone.unique'        => 'This phone number is already registered.',
            'password.regex'      => 'Password must include uppercase, lowercase, number, and special character.',
            'password.confirmed'  => 'Password confirmation does not match.',
        ];
    }
}
