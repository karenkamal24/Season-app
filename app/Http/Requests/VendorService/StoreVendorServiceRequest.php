<?php

namespace App\Http\Requests\VendorService;

use Illuminate\Foundation\Http\FormRequest;

class StoreVendorServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_type_id' => 'required|exists:service_types,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'country_id' => 'nullable|exists:countries,id',
            'commercial_register' => 'nullable|file',
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:5120'],
        ];
    }
}
