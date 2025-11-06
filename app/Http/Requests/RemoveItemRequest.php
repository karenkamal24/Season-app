<?php

namespace App\Http\Requests;

use App\Helpers\LangHelper;
use Illuminate\Foundation\Http\FormRequest;

class RemoveItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'quantity.integer' => LangHelper::msg('quantity_integer'),
            'quantity.min' => LangHelper::msg('quantity_min'),
        ];
    }
}


