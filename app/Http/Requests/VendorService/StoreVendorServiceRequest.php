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
            'contact_number' => ['nullable', 'string', 'regex:/^\+[1-9]\d{7,14}$/'],
            'phone' => ['nullable', 'string', 'regex:/^\+[1-9]\d{7,14}$/'],
            'whatsapp' => ['nullable', 'string', 'regex:/^[1-9]\d{7,14}$/'],
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'country_id' => 'nullable|exists:countries,id',
            'commercial_register' => 'nullable|file',
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'max:5120'],
        ];
    }
}
