<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBagItemRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'item_category_id' => ['required', 'integer', 'exists:item_categories,id'],
            'essential' => ['sometimes', 'boolean'],
            'packed' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:999'],
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
            'name.required' => 'اسم الغرض مطلوب',
            'weight.required' => 'وزن الغرض مطلوب',
            'weight.min' => 'الوزن يجب أن يكون أكبر من صفر',
            'item_category_id.required' => 'فئة الغرض مطلوبة',
            'item_category_id.exists' => 'الفئة المحددة غير موجودة',
            'quantity.min' => 'الكمية يجب أن تكون 1 على الأقل',
        ];
    }
}
