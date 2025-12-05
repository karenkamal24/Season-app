<?php

namespace App\Filament\Resources\Banners\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput as NumberInput;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema
            ->columns(1) // تعيين عمود واحد للعرض الكامل
            ->components([
                Section::make($isArabic ? 'معلومات البانر' : 'Banner Information')
                    ->description($isArabic ? 'أضف صورة للبانر حسب البلد واللغة' : 'Add banner image by country and language')
                    ->schema([
                        Select::make('country_id')
                            ->label($isArabic ? 'البلد' : 'Country')
                            ->relationship('country', $isArabic ? 'name_ar' : 'name_en',
                                fn ($query) => $query->whereIn('code', ['KSA', 'UAE', 'EGY']))
                            ->searchable(['name_en', 'name_ar', 'code'])
                            ->preload()
                            ->required()
                            ->placeholder($isArabic ? 'اختر البلد' : 'Select Country')
                            ->native(false)
                            ->columnSpan(1)
                            ->helperText($isArabic ? 'اختر من البلدان المسجلة فقط' : 'Select from registered countries only'),

                        Select::make('language')
                            ->label($isArabic ? 'اللغة' : 'Language')
                            ->options([
                                'en' => $isArabic ? 'الإنجليزية' : 'English',
                                'ar' => $isArabic ? 'العربية' : 'Arabic',
                            ])
                            ->required()
                            ->placeholder($isArabic ? 'اختر اللغة' : 'Select Language')
                            ->native(false)
                            ->columnSpan(1),

                        FileUpload::make('image')
                            ->label($isArabic ? 'صورة البانر' : 'Banner Image')
                            ->directory('banners')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->columnSpanFull()
                            ->helperText($isArabic ? 'الصورة التي ستظهر لهذا البلد واللغة' : 'Image that will appear for this country and language'),

                        Toggle::make('is_active')
                            ->label($isArabic ? 'نشط' : 'Active')
                            ->default(true)
                            ->required()
                            ->helperText($isArabic ? 'البانرات النشطة فقط ستظهر في التطبيق' : 'Only active banners will appear in the app')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(), // القسم يأخذ العرض الكامل
            ]);
    }
}
