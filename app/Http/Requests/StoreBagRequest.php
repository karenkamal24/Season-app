<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBagRequest extends FormRequest
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
            'trip_type' => ['required', Rule::in(['عمل', 'سياحة', 'عائلية', 'علاج'])],
            'duration' => ['required', 'integer', 'min:1', 'max:365'],
            'destination' => ['required', 'string', 'max:255'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'max_weight' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'status' => ['sometimes', Rule::in(['draft', 'in_progress', 'completed', 'cancelled'])],
            'preferences' => ['sometimes', 'array'],
            'preferences.style' => ['sometimes', 'string'],
            'preferences.priorities' => ['sometimes', 'array'],
            'items' => ['sometimes', 'array'],
            'items.*.name' => ['required_with:items', 'string', 'max:255'],
            'items.*.weight' => ['required_with:items', 'numeric', 'min:0', 'max:999.99'],
            'items.*.item_category_id' => ['required_with:items', 'integer', 'exists:item_categories,id'],
            'items.*.essential' => ['sometimes', 'boolean'],
            'items.*.packed' => ['sometimes', 'boolean'],
            'items.*.notes' => ['nullable', 'string'],
            'items.*.quantity' => ['sometimes', 'integer', 'min:1'],
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
            'name.required' => 'اسم الحقيبة مطلوب',
            'trip_type.required' => 'نوع الرحلة مطلوب',
            'trip_type.in' => 'نوع الرحلة يجب أن يكون: عمل، سياحة، عائلية، أو علاج',
            'duration.required' => 'مدة الرحلة مطلوبة',
            'duration.min' => 'مدة الرحلة يجب أن تكون يوم واحد على الأقل',
            'destination.required' => 'وجهة السفر مطلوبة',
            'departure_date.required' => 'تاريخ المغادرة مطلوب',
            'departure_date.after_or_equal' => 'تاريخ المغادرة يجب أن يكون اليوم أو في المستقبل',
            'max_weight.required' => 'الحد الأقصى للوزن مطلوب',
            'max_weight.min' => 'الحد الأقصى للوزن يجب أن يكون أكبر من صفر',
        ];
    }
}
