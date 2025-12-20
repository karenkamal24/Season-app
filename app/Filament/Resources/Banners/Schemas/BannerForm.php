<?php

namespace App\Filament\Resources\Banners\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema
            ->columns(1)
            ->components([
                Section::make($isArabic ? 'معلومات البانر' : 'Banner Information')
                    ->schema([
                        Select::make('language')
                            ->label($isArabic ? 'اللغة' : 'Language')
                            ->options([
                                'en' => $isArabic ? 'الإنجليزية' : 'English',
                                'ar' => $isArabic ? 'العربية' : 'Arabic',
                            ])
                            ->required(),

                        FileUpload::make('image')
                            ->label($isArabic ? 'صورة البانر' : 'Banner Image')
                            ->disk('public')                // important
                            ->directory('banners')          // saved inside storage/app/public/banners
                            ->visibility('public')          // allow public access
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->columnSpanFull(),

                        Select::make('route')
                            ->label($isArabic ? 'مسار التطبيق' : 'App Route')
                            ->options(self::getRoutes())
                            ->searchable()
                            ->nullable()
                            ->helperText($isArabic ? 'اختر صفحة داخل التطبيق للانتقال إليها عند الضغط على البانر' : 'Select an app screen to navigate to when banner is clicked')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label($isArabic ? 'نشط' : 'Active')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * Get all available routes for banner navigation
     */
    public static function getRoutes(): array
    {
        $isArabic = LanguageHelper::isArabic();

        return [
            // Authentication Routes
            '/login' => $isArabic ? 'تسجيل الدخول (/login)' : 'Login (/login)',
            '/signUp' => $isArabic ? 'إنشاء حساب (/signUp)' : 'Sign Up (/signUp)',
            '/welcome' => $isArabic ? 'شاشة الترحيب (/welcome)' : 'Welcome (/welcome)',
            '/verifyOtp' => $isArabic ? 'التحقق من OTP (/verifyOtp)' : 'Verify OTP (/verifyOtp)',
            '/forgotPassword' => $isArabic ? 'نسيت كلمة المرور (/forgotPassword)' : 'Forgot Password (/forgotPassword)',
            '/verifyResetOtp' => $isArabic ? 'التحقق من OTP لإعادة التعيين (/verifyResetOtp)' : 'Verify Reset OTP (/verifyResetOtp)',
            '/resetPassword' => $isArabic ? 'إعادة تعيين كلمة المرور (/resetPassword)' : 'Reset Password (/resetPassword)',

            // Main App Routes
            '/home' => $isArabic ? 'الصفحة الرئيسية (/home)' : 'Home (/home)',
            '/profile' => $isArabic ? 'الملف الشخصي (/profile)' : 'Profile (/profile)',
            '/profile/edit' => $isArabic ? 'تعديل الملف الشخصي (/profile/edit)' : 'Profile Edit (/profile/edit)',
            '/settings' => $isArabic ? 'الإعدادات (/settings)' : 'Settings (/settings)',
            '/' => $isArabic ? 'شاشة البداية (/)' : 'Splash (/)',

            // Vendor Services Routes
            '/vendor/services' => $isArabic ? 'خدماتي كبائع (/vendor/services)' : 'My Vendor Services (/vendor/services)',
            '/vendor/services/new' => $isArabic ? 'إضافة خدمة بائع جديدة (/vendor/services/new)' : 'New Vendor Service (/vendor/services/new)',
            '/vendor/services/public' => $isArabic ? 'خدمات البائعين العامة (/vendor/services/public)' : 'Public Vendor Services (/vendor/services/public)',

            // Geographical Guides Routes
            '/geographical-directory' => $isArabic ? 'الدليل الجغرافي (/geographical-directory)' : 'Geographical Directory (/geographical-directory)',
            '/apply-as-trader' => $isArabic ? 'التقديم كتاجر (/apply-as-trader)' : 'Apply as Trader (/apply-as-trader)',
            '/my-geographical-services' => $isArabic ? 'خدماتي الجغرافية (/my-geographical-services)' : 'My Geographical Services (/my-geographical-services)',
            '/geographical-guides/new' => $isArabic ? 'إضافة دليل جغرافي جديد (/geographical-guides/new)' : 'New Geographical Guide (/geographical-guides/new)',

            // Digital Directory Routes
            '/categories' => $isArabic ? 'التصنيفات (/categories)' : 'Categories (/categories)',

            // Utility Routes
            '/emergency' => $isArabic ? 'الطوارئ (/emergency)' : 'Emergency (/emergency)',
            '/currency/converter' => $isArabic ? 'محول العملات (/currency/converter)' : 'Currency Converter (/currency/converter)',
            '/location/picker' => $isArabic ? 'اختيار الموقع (/location/picker)' : 'Location Picker (/location/picker)',
            '/webview' => $isArabic ? 'عرض ويب (/webview)' : 'WebView (/webview)',

            // Groups Routes
            '/groups' => $isArabic ? 'قائمة المجموعات (/groups)' : 'Groups List (/groups)',
            '/groups/create' => $isArabic ? 'إنشاء مجموعة (/groups/create)' : 'Create Group (/groups/create)',
            '/groups/join' => $isArabic ? 'الانضمام لمجموعة (/groups/join)' : 'Join Group (/groups/join)',
            '/groups/qr-scanner' => $isArabic ? 'مسح QR للمجموعات (/groups/qr-scanner)' : 'QR Scanner (/groups/qr-scanner)',
        ];
    }
}
