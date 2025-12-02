<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\FusedGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class SettingForm
{
    public static function getSettingOptions(): array
    {
        return [
            'Service Provider' => [
                'en' => 'Service Provider',
                'ar' => 'مزود خدمة'
            ],
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        $settingOptions = self::getSettingOptions();

        return $schema
            ->components([
                Select::make('name_key')
                    ->label($isArabic ? 'اسم الإعداد' : 'Setting Name')
                    ->options(array_combine(
                        array_keys($settingOptions),
                        array_map(fn($opt) => $opt[$isArabic ? 'ar' : 'en'], $settingOptions)
                    ))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) use ($settingOptions) {
                        if ($state && isset($settingOptions[$state])) {
                            $set('name.en', $settingOptions[$state]['en']);
                            $set('name.ar', $settingOptions[$state]['ar']);
                        }
                    })
                    ->dehydrated(false),

                FusedGroup::make([
                    TextInput::make('name.en')
                        ->label($isArabic ? 'الاسم (إنجليزي)' : 'Name (English)')
                        ->required()
                        ->disabled()
                        ->dehydrated(),

                    TextInput::make('name.ar')
                        ->label($isArabic ? 'الاسم (عربي)' : 'Name (Arabic)')
                        ->required()
                        ->disabled()
                        ->dehydrated(),
                ])
                    ->label($isArabic ? 'تفاصيل الاسم' : 'Name Details')
                    ->columns(2)
                    ->visible(fn ($get) => !empty($get('name_key'))),

                FusedGroup::make([
                    TextInput::make('value')
                        ->label($isArabic ? 'القيمة (Value)' : 'Value')
                        ->helperText($isArabic
                            ? 'عدد النقاط التي يحصل عليها المستخدم عند الموافقة على خدمة المزود.'
                            : 'Number of points the user receives when their vendor service is approved.')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->suffix($isArabic ? 'value' : 'value'),

                    TextInput::make('max')
                        ->label($isArabic ? 'الحد الأقصى (Max)' : 'Max')
                        ->helperText($isArabic
                            ? 'الحد الأقصى لعدد الخدمات التي يمكن للمزود إنشاؤها.'
                            : 'Maximum number of services a vendor can create.')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->suffix($isArabic ? 'max' : 'max'),
                ])
                    ->label($isArabic ? 'الإعدادات' : 'Settings')
                    ->columns(2),
            ]);
    }
}
