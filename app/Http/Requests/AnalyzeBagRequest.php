<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnalyzeBagRequest extends FormRequest
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
            'preferences' => ['sometimes', 'array'],
            'preferences.style' => ['sometimes', 'string', 'in:minimalist,standard,luxury'],
            'preferences.priorities' => ['sometimes', 'array'],
            'force_reanalysis' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'preferences.style.in' => 'نمط التفضيل يجب أن يكون: minimalist، standard، أو luxury',
        ];
    }
}
