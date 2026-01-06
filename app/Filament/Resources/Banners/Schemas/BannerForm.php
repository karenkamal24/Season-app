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
                            ->disk('public')
                            ->directory('banners')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->imagePreviewHeight('250')
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                            ->downloadable()
                            ->openable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->columnSpanFull()
                            // الحلول المهمة
                            ->deletable(true)
                            ->preserveFilenames(false)
                            // ده الأهم: متحاولش تحمل الصورة القديمة في Edit
                            ->afterStateHydrated(function (FileUpload $component, $state) {
                                // خلي الـ state فاضي في Edit عشان ميحاولش يحمل الصورة
                                if (request()->routeIs('filament.*.resources.*.edit')) {
                                    $component->state(null);
                                }
                            }),
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
 
            // Main App Routes
            '/home' => $isArabic ? 'الصفحة الرئيسية (/home)' : 'Home (/home)',
            '/home?tab=bag' => $isArabic ? 'صفحة الحقيبة (/home?tab=bag)' : 'Bag Page (/home?tab=bag)',
            '/home?tab=reminders' => $isArabic ? 'صفحة التذكيرات (/home?tab=reminders)' : 'Reminders Page (/home?tab=reminders)',
            '/home?tab=groups' => $isArabic ? 'صفحة المجموعات (/home?tab=groups)' : 'Groups Page (/home?tab=groups)',
            '/home?tab=profile' => $isArabic ? 'صفحة الملف الشخصي (/home?tab=profile)' : 'Profile Page (/home?tab=profile)',
            '/profile' => $isArabic ? 'الملف الشخصي (/profile)' : 'Profile (/profile)',
            '/profile/edit' => $isArabic ? 'تعديل الملف الشخصي (/profile/edit)' : 'Profile Edit (/profile/edit)',
            '/settings' => $isArabic ? 'الإعدادات (/settings)' : 'Settings (/settings)',

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

            // Groups Routes
            '/groups' => $isArabic ? 'قائمة المجموعات (/groups)' : 'Groups List (/groups)',
            '/groups/create' => $isArabic ? 'إنشاء مجموعة (/groups/create)' : 'Create Group (/groups/create)',
            '/groups/join' => $isArabic ? 'الانضمام لمجموعة (/groups/join)' : 'Join Group (/groups/join)',
            '/groups/qr-scanner' => $isArabic ? 'مسح QR للمجموعات (/groups/qr-scanner)' : 'QR Scanner (/groups/qr-scanner)',

            //
        ];
    }
}
