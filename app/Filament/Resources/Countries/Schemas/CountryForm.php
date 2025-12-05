<?php

namespace App\Filament\Resources\Countries\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema
            ->components([
                Section::make($isArabic ? 'معلومات الدولة' : 'Country Information')
                    ->description($isArabic ? 'أدخل تفاصيل الدولة.' : 'Enter the country details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label($isArabic ? 'الاسم بالعربي' : 'Arabic Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('name_en')
                                    ->label($isArabic ? 'الاسم بالإنجليزي' : 'English Name')
                                    ->maxLength(255),

                                Select::make('code')
                                    ->label($isArabic ? 'رمز الدولة' : 'Country Code')
                                    ->options([
                                        'KSA' => 'KSA - ' . ($isArabic ? 'السعودية' : 'Saudi Arabia'),
                                        'UAE' => 'UAE - ' . ($isArabic ? 'الإمارات' : 'United Arab Emirates'),
                                        'EGY' => 'EGY - ' . ($isArabic ? 'مصر' : 'Egypt'),
                                    ])
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->native(false)
                                    ->searchable(false)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
