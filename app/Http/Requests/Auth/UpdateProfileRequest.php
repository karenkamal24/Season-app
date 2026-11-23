<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\LangHelper;

class UpdateProfileRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('nationality'))) {
            $this->merge(['nationality' => strtoupper(trim($this->input('nationality')))]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'nationality' => ['nullable', 'string', 'size:3', 'regex:/^[A-Za-z]{3}$/'],
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
            'email.email'        => LangHelper::msg('email_invalid'),
            'email.unique'       => LangHelper::msg('email_registered'),
            'phone.unique'       => LangHelper::msg('phone_unique'),
            'gender.in'          => LangHelper::msg('gender_invalid'),
            'photo_url.image'    => LangHelper::msg('photo_invalid_format'),
            'birth_date.date'    => LangHelper::msg('birth_date_invalid'),
        ];
    }
}
