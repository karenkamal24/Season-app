<?php

namespace App\Filament\Resources\Countries\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\TextInput;
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

                                TextInput::make('code')
                                    ->label($isArabic ? 'رمز الدولة' : 'Country Code')
                                    ->required()
                                    ->maxLength(3)
                                    ->unique(ignoreRecord: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('code', strtoupper($state)))
                                    ->placeholder($isArabic ? 'مثال: EGY, SAU' : 'e.g., EGY, SAU')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
