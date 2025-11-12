<?php

namespace App\Filament\Resources\EmergencyNumbers\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class EmergencyNumberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                Section::make($isArabic ? 'معلومات الطوارئ' : 'Emergency Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('country.name_ar')
                                    ->label($isArabic ? 'الدولة' : 'Country')
                                    ->placeholder('-')
                                    ->badge()
                                    ->color('info')
                                    ->columnSpan(1),

                                TextEntry::make('country.code')
                                    ->label($isArabic ? 'رمز الدولة' : 'Country Code')
                                    ->placeholder('-')
                                    ->badge()
                                    ->color('success')
                                    ->columnSpan(1),

                                TextEntry::make('fire')
                                    ->label($isArabic ? 'رقم الإطفاء' : 'Fire Number')
                                    ->icon('heroicon-o-fire')
                                    ->placeholder('-'),

                                TextEntry::make('police')
                                    ->label($isArabic ? 'رقم الشرطة' : 'Police Number')
                                    ->icon('heroicon-o-shield-check')
                                    ->placeholder('-'),

                                TextEntry::make('ambulance')
                                    ->label($isArabic ? 'رقم الإسعاف' : 'Ambulance Number')
                                    ->icon('heroicon-m-heart')
                                    ->placeholder('-'),

                                TextEntry::make('embassy')
                                    ->label($isArabic ? 'رقم السفارة' : 'Embassy Number')
                                    ->icon('heroicon-o-building-office')
                                    ->placeholder('-'),
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
