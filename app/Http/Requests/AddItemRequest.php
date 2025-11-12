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
            // Either item_id OR custom_item_name must be provided
            'item_id' => 'required_without:custom_item_name|nullable|exists:items,id',
            'custom_item_name' => 'required_without:item_id|nullable|string|max:255',
            'bag_type_id' => 'nullable|exists:bag_types,id',
            'quantity' => 'nullable|integer|min:1',
            'custom_weight' => 'required_with:custom_item_name|nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'item_id.required_without' => LangHelper::msg('item_id_or_name_required'),
            'item_id.exists' => LangHelper::msg('item_not_found'),
            'custom_item_name.required_without' => LangHelper::msg('item_id_or_name_required'),
            'custom_item_name.string' => LangHelper::msg('custom_item_name_string'),
            'custom_item_name.max' => LangHelper::msg('custom_item_name_max'),
            'custom_weight.required_with' => LangHelper::msg('custom_weight_required_for_custom_item'),
            'bag_type_id.exists' => LangHelper::msg('bag_type_not_found'),
            'quantity.integer' => LangHelper::msg('quantity_integer'),
            'quantity.min' => LangHelper::msg('quantity_min'),
            'custom_weight.numeric' => LangHelper::msg('custom_weight_numeric'),
            'custom_weight.min' => LangHelper::msg('custom_weight_min'),
        ];
    }
}


