<?php

namespace App\Helpers;

class LangHelper
{
    public static function msg(string $key): string
    {
        $locale = app()->getLocale();

        $messages = [

          
            'otp_sent' => [
                'en' => 'OTP sent successfully to your email.',
                'ar' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني بنجاح.'
            ],
            'otp_resent' => [
                'en' => 'A new OTP has been sent to your email.',
                'ar' => 'تم إرسال رمز تحقق جديد إلى بريدك الإلكتروني.'
            ],
            'otp_required' => [
                'en' => 'OTP is required.',
                'ar' => 'رمز التحقق مطلوب.'
            ],
            'otp_digits' => [
                'en' => 'OTP must be exactly 4 digits.',
                'ar' => 'يجب أن يتكون رمز التحقق من 4 أرقام بالضبط.'
            ],
            'otp_not_generated' => [
                'en' => 'OTP not generated.',
                'ar' => 'لم يتم إنشاء رمز التحقق بعد.'
            ],
            'otp_expired' => [
                'en' => 'OTP expired.',
                'ar' => 'انتهت صلاحية رمز التحقق.'
            ],
            'otp_invalid' => [
                'en' => 'Invalid OTP.',
                'ar' => 'رمز التحقق غير صحيح.'
            ],
            'otp_not_verified_yet' => [
                'en' => 'OTP not verified yet.',
                'ar' => 'لم يتم التحقق من رمز التحقق بعد.'
            ],
            'verify_success' => [
                'en' => 'Email verified successfully.',
                'ar' => 'تم التحقق من البريد الإلكتروني بنجاح.'
            ],


            'login_success' => [
                'en' => 'Login successful.',
                'ar' => 'تم تسجيل الدخول بنجاح.'
            ],
            'register_success' => [
                'en' => 'Registration completed successfully.',
                'ar' => 'تم التسجيل بنجاح.'
            ],
            'invalid_credentials' => [
                'en' => 'Invalid email or password.',
                'ar' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.'
            ],
            'not_verified' => [
                'en' => 'Your email is not verified. Please verify your account first.',
                'ar' => 'لم يتم تفعيل بريدك الإلكتروني بعد. يرجى تفعيله أولاً.'
            ],


            'forgot_otp_sent' => [
                'en' => 'OTP sent to your email.',
                'ar' => 'تم إرسال رمز التحقق إلى بريدك الإلكتروني.'
            ],
            'forgot_otp_verified' => [
                'en' => 'OTP verified successfully.',
                'ar' => 'تم التحقق من رمز التحقق بنجاح.'
            ],
            'password_reset' => [
                'en' => 'Password reset successfully.',
                'ar' => 'تم إعادة تعيين كلمة المرور بنجاح.'
            ],
            'forgot_otp_resent' => [
                'en' => 'A new OTP has been sent to your email.',
                'ar' => 'تم إرسال رمز تحقق جديد إلى بريدك الإلكتروني.'
            ],


            'profile_retrieved' => [
                'en' => 'Profile retrieved successfully.',
                'ar' => 'تم جلب الملف الشخصي بنجاح.'
            ],
            'profile_updated' => [
                'en' => 'Profile updated successfully.',
                'ar' => 'تم تحديث الملف الشخصي بنجاح.'
            ],
            'birth_date_invalid' => [
                'en' => 'Please enter a valid date.',
                'ar' => 'يرجى إدخال تاريخ صالح.'
            ],
            'gender_invalid' => [
                'en' => 'Gender must be either male or female.',
                'ar' => 'يجب أن يكون الجنس إما ذكرًا أو أنثى.'
            ],
            'photo_invalid_format' => [
                'en' => 'The file must be an image (jpeg, png, jpg).',
                'ar' => 'يجب أن يكون الملف صورة بصيغة (jpeg, png, jpg).'
            ],


            'vendor_services_retrieved' => [
                'en' => 'Vendor services retrieved successfully.',
                'ar' => 'تم جلب خدمات البائع بنجاح.'
            ],
            'vendor_service_details' => [
                'en' => 'Vendor service details retrieved successfully.',
                'ar' => 'تم جلب تفاصيل خدمة البائع بنجاح.'
            ],
            'vendor_service_created' => [
                'en' => 'Vendor service created successfully and is pending admin approval.',
                'ar' => 'تم إنشاء خدمة البائع بنجاح وهي بانتظار موافقة الإدارة.'
            ],
            'vendor_service_updated' => [
                'en' => 'Vendor service updated successfully.',
                'ar' => 'تم تحديث خدمة البائع بنجاح.'
            ],
            'vendor_service_disabled' => [
                'en' => 'Vendor service disabled successfully.',
                'ar' => 'تم تعطيل خدمة البائع بنجاح.'
            ],
            'vendor_service_not_found' => [
                'en' => 'Vendor service not found.',
                'ar' => 'لم يتم العثور على خدمة البائع.'
            ],
            'vendor_service_limit_reached' => [
                'en' => 'You have reached the maximum number of vendor services allowed.',
                'ar' => 'لقد وصلت إلى الحد الأقصى المسموح به من خدمات البائع.'
            ],
            'vendor_service_cannot_delete' => [
                'en' => 'This service cannot be deleted.',
                'ar' => 'لا يمكن حذف هذه الخدمة.'
            ],
            'service_types_retrieved' => [
                'en' => 'Service types retrieved successfully.',
                'ar' => 'تم جلب أنواع الخدمات بنجاح.'
            ],

            'emergency_missing_header' => [
                'en' => 'Missing Accept-Country header.',
                'ar' => 'رأس Accept-Country مفقود.'
            ],
            'emergency_not_found' => [
                'en' => 'Emergency numbers not found for this country.',
                'ar' => 'لم يتم العثور على أرقام الطوارئ لهذا البلد.'
            ],
            'emergency_fetched' => [
                'en' => 'Emergency numbers fetched successfully.',
                'ar' => 'تم جلب أرقام الطوارئ بنجاح.'
            ],

            'countries_fetched' => [
                'en' => 'Countries fetched successfully.',
                'ar' => 'تم جلب الدول بنجاح.'
            ],
            'country_fetched' => [
                'en' => 'Country fetched successfully.',
                'ar' => 'تم جلب الدولة بنجاح.'
            ],
            'country_not_found' => [
                'en' => 'Country not found.',
                'ar' => 'لم يتم العثور على الدولة.'
            ],
            'cities_fetched' => [
                'en' => 'Cities fetched successfully.',
                'ar' => 'تم جلب المدن بنجاح.'
            ],
            'city_fetched' => [
                'en' => 'City fetched successfully.',
                'ar' => 'تم جلب المدينة بنجاح.'
            ],
            'city_not_found' => [
                'en' => 'City not found.',
                'ar' => 'لم يتم العثور على المدينة.'
            ],


            'email_required' => [
                'en' => 'Email is required.',
                'ar' => 'البريد الإلكتروني مطلوب.'
            ],
            'email_invalid' => [
                'en' => 'Please enter a valid email address.',
                'ar' => 'يرجى إدخال بريد إلكتروني صالح.'
            ],
            'email_registered' => [
                'en' => 'This email is already taken.',
                'ar' => 'هذا البريد الإلكتروني مستخدم بالفعل.'
            ],
            'email_not_found' => [
                'en' => 'Email not found.',
                'ar' => 'البريد الإلكتروني غير موجود.'
            ],
            'phone_invalid' => [
                'en' => 'Please enter a valid international phone number.',
                'ar' => 'يرجى إدخال رقم هاتف دولي صالح.'
            ],
            'phone_regex' => [
                'en' => 'Phone number must start with + and include country code.',
                'ar' => 'يجب أن يبدأ رقم الهاتف بـ + ويتضمن كود الدولة.'
            ],
            'phone_unique' => [
                'en' => 'This phone number is already registered.',
                'ar' => 'رقم الهاتف مسجل بالفعل.'
            ],


            'password_regex_invalid' => [
                'en' => 'Password must include uppercase, lowercase, number, and special character.',
                'ar' => 'يجب أن تحتوي كلمة المرور على أحرف كبيرة وصغيرة وأرقام ورموز خاصة.'
            ],
            'password_confirm_mismatch' => [
                'en' => 'Password confirmation does not match.',
                'ar' => 'تأكيد كلمة المرور غير متطابق.'
            ],


            'first_name_required' => [
                'en' => 'First name is required.',
                'ar' => 'الاسم الأول مطلوب.'
            ],
            'last_name_required' => [
                'en' => 'Last name is required.',
                'ar' => 'اسم العائلة مطلوب.'
            ],
            'first_name_regex' => [
                'en' => 'First name must contain only letters.',
                'ar' => 'يجب أن يحتوي الاسم الأول على أحرف فقط.'
            ],
            'last_name_regex' => [
                'en' => 'Last name must contain only letters.',
                'ar' => 'يجب أن يحتوي اسم العائلة على أحرف فقط.'
            ],
        ];

        return $messages[$key][$locale] ?? $messages[$key]['en'] ?? $key;
    }
}
