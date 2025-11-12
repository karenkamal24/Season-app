<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\FusedGroup;
use Filament\Forms\Components\TextInput;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                FusedGroup::make([
                    TextInput::make('name.en')
                        ->label($isArabic ? 'الاسم (إنجليزي)' : 'Name (English)')
                        ->placeholder($isArabic ? 'أدخل الاسم بالإنجليزي' : 'Enter English name')
                        ->required(),

                    TextInput::make('name.ar')
                        ->label($isArabic ? 'الاسم (عربي)' : 'Name (Arabic)')
                        ->placeholder($isArabic ? 'ادخل الاسم بالعربية' : 'Enter Arabic name')
                        ->required(),
                ])
                    ->label($isArabic ? 'اسم الإعداد' : 'Setting Name')
                    ->columns(2),

                FusedGroup::make([
                    TextInput::make('value')
                        ->label($isArabic ? 'القيمة' : 'Value')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->suffix($isArabic ? 'نقطة' : 'points'),

                    TextInput::make('max')
                        ->label($isArabic ? 'الحد الأقصى للنقاط' : 'Max Points')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->suffix($isArabic ? 'نقطة' : 'points')
                        ->helperText($isArabic ? 'الحد الأقصى لعدد النقاط المسموح بها.' : 'Maximum number of points allowed.'),
                ])
                    ->label($isArabic ? 'إعدادات النقاط' : 'Points Configuration')
                    ->columns(2),
            ]);
    }
}
