<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBagRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'trip_type' => ['sometimes', Rule::in(['عمل', 'سياحة', 'عائلية', 'علاج'])],
            'duration' => ['sometimes', 'integer', 'min:1', 'max:365'],
            'destination' => ['sometimes', 'string', 'max:255'],
            'departure_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'max_weight' => ['sometimes', 'numeric', 'min:0', 'max:999.99'],
            'status' => ['sometimes', Rule::in(['draft', 'in_progress', 'completed', 'cancelled'])],
            'preferences' => ['sometimes', 'array'],
            'preferences.style' => ['sometimes', 'string'],
            'preferences.priorities' => ['sometimes', 'array'],
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
            'name.string' => 'اسم الحقيبة يجب أن يكون نص',
            'trip_type.in' => 'نوع الرحلة يجب أن يكون: عمل، سياحة، عائلية، أو علاج',
            'duration.min' => 'مدة الرحلة يجب أن تكون يوم واحد على الأقل',
            'departure_date.after_or_equal' => 'تاريخ المغادرة يجب أن يكون اليوم أو في المستقبل',
            'max_weight.min' => 'الحد الأقصى للوزن يجب أن يكون أكبر من صفر',
        ];
    }
}
