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
            'bag_type_id' => 'required|exists:bag_types,id',
            'quantity' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'bag_type_id.required' => LangHelper::msg('bag_type_id_required'),
            'bag_type_id.exists' => LangHelper::msg('bag_type_not_found'),
            'quantity.integer' => LangHelper::msg('quantity_integer'),
            'quantity.min' => LangHelper::msg('quantity_min'),
        ];
    }
}
