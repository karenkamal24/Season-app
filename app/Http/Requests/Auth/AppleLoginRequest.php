<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AppleLoginRequest extends FormRequest
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
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ], 422));
    }

    public function rules(): array
    {
        return [
            'id_token' => 'required|string',
            'authorization_code' => 'nullable|string',
            'fcm_token' => 'nullable|string',
            'bundle_id' => 'nullable|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
        ];
    }
}
