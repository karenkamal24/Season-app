<?php

namespace App\Http\Requests\GeographicalGuide;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\LanguageHelper;

class UpdateGeographicalGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'geographical_category_id' => 'sometimes|exists:geographical_categories,id',
            'geographical_sub_category_id' => 'nullable|exists:geographical_sub_categories,id',
            'service_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone_1' => 'nullable|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'country_id' => 'sometimes|exists:countries,id',
            'city_id' => 'sometimes|exists:cities,id',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'website' => 'nullable|url|max:255',
            'commercial_register' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'establishment_number' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        $isArabic = LanguageHelper::isArabic();

        return [
            'geographical_category_id.exists' => $isArabic ? 'التصنيف المحدد غير موجود' : 'Selected category does not exist',
            'geographical_sub_category_id.exists' => $isArabic ? 'التصنيف الفرعي المحدد غير موجود' : 'Selected sub category does not exist',
            'service_name.max' => $isArabic ? 'اسم الخدمة لا يمكن أن يكون أكثر من 255 حرف' : 'Service name cannot exceed 255 characters',
            'description.max' => $isArabic ? 'الوصف لا يمكن أن يكون أكثر من 1000 حرف' : 'Description cannot exceed 1000 characters',
            'phone_1.max' => $isArabic ? 'رقم الهاتف الأول لا يمكن أن يكون أكثر من 20 حرف' : 'Phone 1 cannot exceed 20 characters',
            'phone_2.max' => $isArabic ? 'رقم الهاتف الثاني لا يمكن أن يكون أكثر من 20 حرف' : 'Phone 2 cannot exceed 20 characters',
            'country_id.exists' => $isArabic ? 'الدولة المحددة غير موجودة' : 'Selected country does not exist',
            'city_id.exists' => $isArabic ? 'المدينة المحددة غير موجودة' : 'Selected city does not exist',
            'address.max' => $isArabic ? 'العنوان لا يمكن أن يكون أكثر من 500 حرف' : 'Address cannot exceed 500 characters',
            'latitude.numeric' => $isArabic ? 'خط العرض يجب أن يكون رقماً' : 'Latitude must be a number',
            'latitude.between' => $isArabic ? 'خط العرض يجب أن يكون بين -90 و 90' : 'Latitude must be between -90 and 90',
            'longitude.numeric' => $isArabic ? 'خط الطول يجب أن يكون رقماً' : 'Longitude must be a number',
            'longitude.between' => $isArabic ? 'خط الطول يجب أن يكون بين -180 و 180' : 'Longitude must be between -180 and 180',
            'website.url' => $isArabic ? 'الموقع الإلكتروني يجب أن يكون رابطاً صحيحاً' : 'Website must be a valid URL',
            'website.max' => $isArabic ? 'الموقع الإلكتروني لا يمكن أن يكون أكثر من 255 حرف' : 'Website cannot exceed 255 characters',
            'commercial_register.file' => $isArabic ? 'السجل التجاري يجب أن يكون ملفاً' : 'Commercial register must be a file',
            'commercial_register.mimes' => $isArabic ? 'السجل التجاري يجب أن يكون بصيغة PDF, JPG, JPEG, أو PNG' : 'Commercial register must be a PDF, JPG, JPEG, or PNG file',
            'commercial_register.max' => $isArabic ? 'حجم ملف السجل التجاري لا يمكن أن يتجاوز 5 ميجابايت' : 'Commercial register file size cannot exceed 5MB',
            'establishment_number.max' => $isArabic ? 'رقم المنشأة لا يمكن أن يكون أكثر من 255 حرف' : 'Establishment number cannot exceed 255 characters',
        ];
    }
}

