<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
public function rules(): array
{
    return [
        'name' => 'nullable|string|max:255',
        'nickname' => 'nullable|string|max:255',
        'email' => [
            'nullable',
            'email',
            Rule::unique('users', 'email')->ignore($this->user()->id),
        ],
        'phone' => [
            'nullable',
            'string',
            'max:20',
            Rule::unique('users', 'phone')->ignore($this->user()->id),
        ],
        'birth_date' => 'nullable|date',
        'gender' => 'nullable|in:male,female',
        'photo_url' => 'nullable|image|max:2048',
    ];
}

    public function messages(): array
    {
        return [
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'phone.unique' => 'This phone number is already registered.',
            'gender.in' => 'Gender must be either male or female.',
            'photo_url.image' => 'The file must be an image (jpeg, png, jpg).',
        ];
    }
}
