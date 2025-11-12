<?php

namespace App\Filament\Resources\Countries\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CountryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                Section::make($isArabic ? 'تفاصيل الدولة' : 'Country Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name_ar')
                                    ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name')
                                    ->placeholder('-')
                                    ->weight('bold'),

                                TextEntry::make('name_en')
                                    ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name')
                                    ->placeholder('-'),

                                TextEntry::make('code')
                                    ->label($isArabic ? 'رمز الدولة' : 'Country Code')
                                    ->badge()
                                    ->color('primary'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make($isArabic ? 'التواريخ' : 'Timestamps')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label($isArabic ? 'تاريخ الإنشاء' : 'Created At')
                                    ->dateTime()
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label($isArabic ? 'تاريخ التحديث' : 'Updated At')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}
