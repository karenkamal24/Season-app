<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema
            ->components([
                Section::make($isArabic ? 'المعلومات الشخصية' : 'Personal Information')
                    ->description($isArabic ? 'المعلومات الأساسية للمستخدم.' : 'Basic user information.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label($isArabic ? 'الاسم الكامل' : 'Full Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('nickname')
                                    ->label($isArabic ? 'الاسم المستعار' : 'Nickname')
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label($isArabic ? 'البريد الإلكتروني' : 'Email Address')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label($isArabic ? 'رقم الهاتف' : 'Phone Number')
                                    ->tel()
                                    ->maxLength(20),

                                DatePicker::make('birth_date')
                                    ->label($isArabic ? 'تاريخ الميلاد' : 'Date of Birth')
                                    ->native(false)
                                    ->maxDate(now()),

                                Select::make('gender')
                                    ->label($isArabic ? 'الجنس' : 'Gender')
                                    ->options([
                                        'male' => $isArabic ? 'ذكر' : 'Male',
                                        'female' => $isArabic ? 'أنثى' : 'Female',
                                    ])
                                    ->native(false),

                                FileUpload::make('photo_url')
                                    ->label($isArabic ? 'صورة الملف الشخصي' : 'Profile Picture')
                                    ->image()
                                    ->directory('avatars')
                                    ->visibility('public')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make($isArabic ? 'الموقع والإعدادات' : 'Location & Settings')
                    ->description($isArabic ? 'موقع المستخدم وتفضيلاته.' : 'User location and preferences.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('address')
                                    ->label($isArabic ? 'العنوان' : 'Address')
                                    ->maxLength(255)
                                    ->columnSpanFull(),

                                TextInput::make('city')
                                    ->label($isArabic ? 'المدينة' : 'City')
                                    ->maxLength(100),

                                TextInput::make('currency')
                                    ->label($isArabic ? 'العملة' : 'Currency')
                                    ->maxLength(10)
                                    ->placeholder($isArabic ? 'مثال: EGP, USD' : 'e.g., EGP, USD'),

                                TextInput::make('language')
                                    ->label($isArabic ? 'اللغة المفضلة' : 'Preferred Language')
                                    ->maxLength(10)
                                    ->placeholder($isArabic ? 'مثال: ar, en' : 'e.g., ar, en'),

                                TextInput::make('lat')
                                    ->label($isArabic ? 'خط العرض' : 'Latitude')
                                    ->numeric()
                                    ->placeholder($isArabic ? 'مثال: 30.0444' : 'e.g., 30.0444'),

                                TextInput::make('lng')
                                    ->label($isArabic ? 'خط الطول' : 'Longitude')
                                    ->numeric()
                                    ->placeholder($isArabic ? 'مثال: 31.2357' : 'e.g., 31.2357'),
                            ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),

                Section::make($isArabic ? 'إعدادات الحساب' : 'Account Settings')
                    ->description($isArabic ? 'الدور والحالة والمصادقة.' : 'Role, status, and authentication.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('role')
                                    ->label($isArabic ? 'دور المستخدم' : 'User Role')
                                    ->options([
                                        'customer' => $isArabic ? 'عميل' : 'Customer',
                                        'admin' => $isArabic ? 'مدير' : 'Admin',
                                        'vendor' => $isArabic ? 'بائع' : 'Vendor',
                                    ])
                                    ->required()
                                    ->default('customer')
                                    ->native(false),

                                TextInput::make('password')
                                    ->label($isArabic ? 'كلمة المرور' : 'Password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->maxLength(255),

                                Toggle::make('is_blocked')
                                    ->label($isArabic ? 'حظر المستخدم' : 'Block User')
                                    ->inline(false)
                                    ->default(false),

                                Toggle::make('is_vendor')
                                    ->label($isArabic ? 'هو بائع' : 'Is Vendor')
                                    ->inline(false)
                                    ->default(false),

                                Toggle::make('has_interests')
                                    ->label($isArabic ? 'لديه اهتمامات' : 'Has Interests')
                                    ->inline(false)
                                    ->default(false),

                                DateTimePicker::make('email_verified_at')
                                    ->label($isArabic ? 'تم التحقق من البريد' : 'Email Verified At')
                                    ->native(false),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make($isArabic ? 'تسجيل الدخول الاجتماعي' : 'Social Login')
                    ->description($isArabic ? 'تفاصيل المصادقة من طرف ثالث.' : 'Third-party authentication details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('provider')
                                    ->label($isArabic ? 'الموفر' : 'Provider')
                                    ->maxLength(50)
                                    ->placeholder($isArabic ? 'مثال: google, facebook' : 'e.g., google, facebook'),

                                TextInput::make('provider_id')
                                    ->label($isArabic ? 'معرف الموفر' : 'Provider ID')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),

                Section::make($isArabic ? 'الإحصائيات وOTP' : 'Statistics & OTP')
                    ->description($isArabic ? 'نشاط المستخدم والتحقق.' : 'User activity and verification.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('request')
                                    ->label($isArabic ? 'الطلبات' : 'Requests')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('coins')
                                    ->label($isArabic ? 'النقاط' : 'Coins')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('trips')
                                    ->label($isArabic ? 'الرحلات' : 'Trips')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),

                                TextInput::make('last_otp')
                                    ->label($isArabic ? 'OTP الأخير' : 'Last OTP')
                                    ->maxLength(10),

                                DateTimePicker::make('last_otp_expire')
                                    ->label($isArabic ? 'انتهاء OTP' : 'OTP Expiration')
                                    ->native(false),
                            ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}
