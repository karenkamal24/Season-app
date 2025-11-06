<?php

namespace App\Http\Requests;

use App\Helpers\LangHelper;
use Illuminate\Foundation\Http\FormRequest;

class AddItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_id' => 'required|exists:items,id',
            'quantity' => 'nullable|integer|min:1',
            'custom_weight' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required' => LangHelper::msg('item_id_required'),
            'item_id.exists' => LangHelper::msg('item_not_found'),
            'quantity.integer' => LangHelper::msg('quantity_integer'),
            'quantity.min' => LangHelper::msg('quantity_min'),
            'custom_weight.numeric' => LangHelper::msg('custom_weight_numeric'),
            'custom_weight.min' => LangHelper::msg('custom_weight_min'),
        ];
    }
}


