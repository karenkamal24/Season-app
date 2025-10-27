<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'safety_radius' => 'sometimes|integer|min:50|max:5000',
            'notifications_enabled' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المجموعة مطلوب',
            'name.max' => 'اسم المجموعة يجب أن لا يتجاوز 255 حرف',
            'description.max' => 'الوصف يجب أن لا يتجاوز 1000 حرف',
            'safety_radius.min' => 'نطاق الأمان يجب أن يكون 50 متر على الأقل',
            'safety_radius.max' => 'نطاق الأمان يجب أن لا يتجاوز 5000 متر',
        ];
    }
}

