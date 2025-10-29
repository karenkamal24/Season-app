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
            'language_updated' => [
                'en' => 'Language updated successfully.',
                'ar' => 'تم تحديث اللغة بنجاح.'
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
                'en' => 'Vendor service updated successfully and is pending admin approval.',
                'ar' => 'تم تحديث خدمة البائع بنجاح وهي بانتظار موافقة الإدارة.'
            ],
            'vendor_service_disabled' => [
                'en' => 'Vendor service disabled successfully.',
                'ar' => 'تم تعطيل خدمة البائع بنجاح.'
            ],
            'vendor_service_enabled' => [
                'en' => 'Vendor service enabled successfully and is pending admin approval.',
                'ar' => 'تم تفعيل خدمة البائع بنجاح وهي بانتظار موافقة الإدارة.'
            ],
            'vendor_service_already_active' => [
                'en' => 'This service is already active.',
                'ar' => 'هذه الخدمة مفعلة بالفعل.'
            ],
            'vendor_service_deleted_permanently' => [
                'en' => 'Vendor service deleted permanently.',
                'ar' => 'تم حذف خدمة البائع نهائياً.'
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
            'user_id_required' => [
                'en' => 'User ID is required.',
                'ar' => 'مُعرّف المستخدم مطلوب.'
            ],
            'user_id_integer' => [
                'en' => 'User ID must be an integer.',
                'ar' => 'يجب أن يكون مُعرّف المستخدم رقمًا صحيحًا.'
            ],
            'user_id_not_found' => [
                'en' => 'User not found.',
                'ar' => 'لم يتم العثور على المستخدم.'
            ],
            'validation_failed' => [
                'en' => 'Validation failed.',
                'ar' => 'فشل التحقق من البيانات.'
            ],

            'qr_generated_successfully' => [
                'en' => 'QR code generated successfully.',
                'ar' => 'تم إنشاء رمز الاستجابة السريعة (QR) بنجاح.'
            ],
            'qr_generation_failed' => [
                'en' => 'Failed to generate QR code.',
                'ar' => 'فشل إنشاء رمز الاستجابة السريعة (QR).'
            ],

            // Groups System Messages
            'groups_fetched' => [
                'en' => 'Groups retrieved successfully.',
                'ar' => 'تم جلب المجموعات بنجاح.'
            ],
            'groups_fetch_failed' => [
                'en' => 'Failed to retrieve groups.',
                'ar' => 'فشل جلب المجموعات.'
            ],
            'group_created' => [
                'en' => 'Group created successfully.',
                'ar' => 'تم إنشاء المجموعة بنجاح.'
            ],
            'group_create_failed' => [
                'en' => 'Failed to create group.',
                'ar' => 'فشل إنشاء المجموعة.'
            ],
            'group_fetched' => [
                'en' => 'Group details retrieved successfully.',
                'ar' => 'تم جلب بيانات المجموعة بنجاح.'
            ],
            'group_fetch_failed' => [
                'en' => 'Failed to retrieve group details.',
                'ar' => 'فشل جلب بيانات المجموعة.'
            ],
            'group_updated' => [
                'en' => 'Group updated successfully.',
                'ar' => 'تم تحديث المجموعة بنجاح.'
            ],
            'group_update_failed' => [
                'en' => 'Failed to update group.',
                'ar' => 'فشل تحديث المجموعة.'
            ],
            'group_deleted' => [
                'en' => 'Group deleted successfully.',
                'ar' => 'تم حذف المجموعة بنجاح.'
            ],
            'group_delete_failed' => [
                'en' => 'Failed to delete group.',
                'ar' => 'فشل حذف المجموعة.'
            ],
            'group_not_found' => [
                'en' => 'Group not found.',
                'ar' => 'المجموعة غير موجودة.'
            ],
            'group_not_member' => [
                'en' => 'You are not a member of this group.',
                'ar' => 'أنت لست عضواً في هذه المجموعة.'
            ],
            'group_no_permission' => [
                'en' => 'You do not have permission to access this group.',
                'ar' => 'ليس لديك صلاحية لعرض هذه المجموعة.'
            ],
            'group_only_owner' => [
                'en' => 'Only the group owner can perform this action.',
                'ar' => 'يمكن لمالك المجموعة فقط القيام بهذا الإجراء.'
            ],

            // Group Members
            'group_joined' => [
                'en' => 'You have successfully joined the group.',
                'ar' => 'تم الانضمام للمجموعة بنجاح.'
            ],
            'group_join_failed' => [
                'en' => 'Failed to join group.',
                'ar' => 'فشل الانضمام للمجموعة.'
            ],
            'group_already_member' => [
                'en' => 'You are already a member of this group.',
                'ar' => 'أنت عضو في هذه المجموعة بالفعل.'
            ],
            'group_left' => [
                'en' => 'You have successfully left the group.',
                'ar' => 'تم مغادرة المجموعة بنجاح.'
            ],
            'group_leave_failed' => [
                'en' => 'Failed to leave group.',
                'ar' => 'فشل مغادرة المجموعة.'
            ],
            'group_owner_cannot_leave' => [
                'en' => 'Owner cannot leave the group. Please delete the group or transfer ownership first.',
                'ar' => 'لا يمكن للمالك مغادرة المجموعة. يرجى حذف المجموعة أو نقل الملكية أولاً.'
            ],
            'group_member_removed' => [
                'en' => 'Member removed from group successfully.',
                'ar' => 'تم إزالة العضو من المجموعة بنجاح.'
            ],
            'group_member_remove_failed' => [
                'en' => 'Failed to remove member from group.',
                'ar' => 'فشل إزالة العضو.'
            ],
            'group_cannot_remove_owner' => [
                'en' => 'Cannot remove the group owner.',
                'ar' => 'لا يمكن إزالة مالك المجموعة.'
            ],
            'group_members_fetched' => [
                'en' => 'Group members retrieved successfully.',
                'ar' => 'تم جلب أعضاء المجموعة بنجاح.'
            ],
            'group_members_fetch_failed' => [
                'en' => 'Failed to retrieve group members.',
                'ar' => 'فشل جلب أعضاء المجموعة.'
            ],
            'group_members_no_permission' => [
                'en' => 'You do not have permission to view group members.',
                'ar' => 'ليس لديك صلاحية لعرض أعضاء هذه المجموعة.'
            ],

            // Invite Codes
            'group_invite_fetched' => [
                'en' => 'Invite details retrieved successfully.',
                'ar' => 'تم جلب معلومات الدعوة بنجاح.'
            ],
            'group_invite_invalid' => [
                'en' => 'Invalid or expired invite code.',
                'ar' => 'كود الدعوة غير صحيح أو منتهي الصلاحية.'
            ],
            'group_invite_code_required' => [
                'en' => 'Invite code is required.',
                'ar' => 'كود الدعوة مطلوب.'
            ],

            // Location Tracking
            'location_updated' => [
                'en' => 'Location updated successfully.',
                'ar' => 'تم تحديث الموقع بنجاح.'
            ],
            'location_update_failed' => [
                'en' => 'Failed to update location.',
                'ar' => 'فشل تحديث الموقع.'
            ],
            'latitude_required' => [
                'en' => 'Latitude is required.',
                'ar' => 'خط العرض مطلوب.'
            ],
            'latitude_invalid' => [
                'en' => 'Latitude must be between -90 and 90.',
                'ar' => 'خط العرض يجب أن يكون بين -90 و 90.'
            ],
            'longitude_required' => [
                'en' => 'Longitude is required.',
                'ar' => 'خط الطول مطلوب.'
            ],
            'longitude_invalid' => [
                'en' => 'Longitude must be between -180 and 180.',
                'ar' => 'خط الطول يجب أن يكون بين -180 و 180.'
            ],

            // SOS Alerts
            'sos_sent' => [
                'en' => 'SOS alert sent successfully.',
                'ar' => 'تم إرسال إشارة SOS بنجاح.'
            ],
            'sos_send_failed' => [
                'en' => 'Failed to send SOS alert.',
                'ar' => 'فشل إرسال إشارة SOS.'
            ],
            'sos_resolved' => [
                'en' => 'SOS alert resolved successfully.',
                'ar' => 'تم إغلاق إشارة SOS.'
            ],
            'sos_resolve_failed' => [
                'en' => 'Failed to resolve SOS alert.',
                'ar' => 'فشل إغلاق إشارة SOS.'
            ],
            'sos_not_found' => [
                'en' => 'SOS alert not found.',
                'ar' => 'إشارة SOS غير موجودة.'
            ],
            'sos_no_permission' => [
                'en' => 'You do not have permission to resolve this SOS alert.',
                'ar' => 'ليس لديك صلاحية.'
            ],
            'sos_message_max' => [
                'en' => 'SOS message must not exceed 500 characters.',
                'ar' => 'رسالة SOS يجب أن لا تتجاوز 500 حرف.'
            ],

            // Group Validation
            'group_name_required' => [
                'en' => 'Group name is required.',
                'ar' => 'اسم المجموعة مطلوب.'
            ],
            'group_name_max' => [
                'en' => 'Group name must not exceed 255 characters.',
                'ar' => 'اسم المجموعة يجب أن لا يتجاوز 255 حرف.'
            ],
            'group_description_max' => [
                'en' => 'Group description must not exceed 1000 characters.',
                'ar' => 'وصف المجموعة يجب أن لا يتجاوز 1000 حرف.'
            ],
            'group_safety_radius_min' => [
                'en' => 'Safety radius must be at least 50 meters.',
                'ar' => 'نطاق الأمان يجب أن يكون 50 متر على الأقل.'
            ],
            'group_safety_radius_max' => [
                'en' => 'Safety radius must not exceed 5000 meters.',
                'ar' => 'نطاق الأمان يجب أن لا يتجاوز 5000 متر.'
            ],

        ];

        return $messages[$key][$locale] ?? $messages[$key]['en'] ?? $key;
    }
}
