<?php

namespace App\Http\Requests\VendorService;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_type_id' => 'nullable|exists:service_types,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'commercial_register' => 'nullable|file',
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:5120'],
        ];
    }
}
