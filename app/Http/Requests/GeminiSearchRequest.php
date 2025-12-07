<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeminiSearchRequest extends FormRequest
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
            'query' => 'required|string|min:1|max:10000',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_output_tokens' => 'nullable|integer|min:1|max:8192',
            'top_p' => 'nullable|numeric|min:0|max:1',
            'top_k' => 'nullable|integer|min:1|max:40',
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
            'query.required' => 'The search query is required.',
            'query.string' => 'The search query must be a string.',
            'query.min' => 'The search query must be at least 1 character.',
            'query.max' => 'The search query must not exceed 10000 characters.',
            'temperature.numeric' => 'Temperature must be a number.',
            'temperature.min' => 'Temperature must be at least 0.',
            'temperature.max' => 'Temperature must not exceed 2.',
            'max_output_tokens.integer' => 'Max output tokens must be an integer.',
            'max_output_tokens.min' => 'Max output tokens must be at least 1.',
            'max_output_tokens.max' => 'Max output tokens must not exceed 8192.',
        ];
    }
}

