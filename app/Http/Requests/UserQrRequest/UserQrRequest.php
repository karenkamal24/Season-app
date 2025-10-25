<?php

namespace App\Http\Requests\UserQrRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\LangHelper;
use Symfony\Component\HttpFoundation\Response;

class UserQrRequest extends FormRequest
{


    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => LangHelper::msg('user_id_required'),
            'user_id.integer' => LangHelper::msg('user_id_integer'),
            'user_id.exists' => LangHelper::msg('user_id_not_found'),
        ];
    }


}
