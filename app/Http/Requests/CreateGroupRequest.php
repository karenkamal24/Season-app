<?php

namespace App\Http\Requests;

use App\Helpers\LangHelper;
use Illuminate\Foundation\Http\FormRequest;

class CreateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'safety_radius' => 'nullable|integer|min:10|max:5000',
            'notifications_enabled' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => LangHelper::msg('group_name_required'),
            'name.max' => LangHelper::msg('group_name_max'),
            'description.max' => LangHelper::msg('group_description_max'),
            'safety_radius.min' => LangHelper::msg('group_safety_radius_min'),
            'safety_radius.max' => LangHelper::msg('group_safety_radius_max'),
        ];
    }
}

