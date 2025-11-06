<?php

namespace App\Http\Requests;

use App\Helpers\LangHelper;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMaxWeightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bag_type_id' => 'nullable|exists:bag_types,id',
            'max_weight' => 'required|numeric|min:0',
            'weight_unit' => 'nullable|string|in:kg,lb',
        ];
    }

    public function messages(): array
    {
        return [
            'bag_type_id.exists' => LangHelper::msg('bag_type_not_found'),
            'max_weight.required' => LangHelper::msg('max_weight_required'),
            'max_weight.numeric' => LangHelper::msg('max_weight_numeric'),
            'max_weight.min' => LangHelper::msg('max_weight_min'),
            'weight_unit.in' => LangHelper::msg('weight_unit_invalid'),
        ];
    }
}


