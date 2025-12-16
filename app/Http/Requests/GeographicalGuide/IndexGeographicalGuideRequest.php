<?php

namespace App\Http\Requests\GeographicalGuide;

use Illuminate\Foundation\Http\FormRequest;

class IndexGeographicalGuideRequest extends FormRequest
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
            'city_id' => 'nullable|exists:cities,id',
            'geographical_category_id' => 'nullable|exists:geographical_categories,id',
            'geographical_sub_category_id' => 'nullable|exists:geographical_sub_categories,id',
        ];
    }
}
