<?php

namespace App\Filament\Resources\Cities\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                Section::make($isArabic ? 'معلومات المدينة' : 'City Information')
                    ->description($isArabic ? 'أدخل تفاصيل المدينة.' : 'Enter the city details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('country_id')
                                    ->label($isArabic ? 'الدولة' : 'Country')
                                    ->relationship('country', 'name_ar')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('name_ar')
                                    ->label($isArabic ? 'الاسم بالعربي' : 'Arabic Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('name_en')
                                    ->label($isArabic ? 'الاسم بالإنجليزي' : 'English Name')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
