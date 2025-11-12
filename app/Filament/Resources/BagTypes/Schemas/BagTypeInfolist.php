<?php

namespace App\Filament\Resources\BagTypes\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class BagTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                Section::make($isArabic ? 'معلومات نوع الحقيبة' : 'Bag Type Information')
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

                                TextEntry::make('default_max_weight')
                                    ->label($isArabic ? 'الوزن الأقصى الافتراضي' : 'Default Max Weight')
                                    ->formatStateUsing(fn($state) => $state ? $state . ' kg' : '-')
                                    ->placeholder('-'),

                                IconEntry::make('is_active')
                                    ->boolean()
                                    ->label($isArabic ? 'نشط' : 'Is Active'),

                                TextEntry::make('travelBags_count')
                                    ->label($isArabic ? 'عدد الحقائب' : 'Travel Bags Count')
                                    ->formatStateUsing(fn($state) => $state ?? 0)
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make($isArabic ? 'الأوصاف' : 'Descriptions')
                    ->schema([
                        TextEntry::make('description_ar')
                            ->label($isArabic ? 'الوصف بالعربي' : 'Arabic Description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('description_en')
                            ->label($isArabic ? 'الوصف بالإنجليزي' : 'English Description')
                            ->placeholder('-')
                            ->columnSpanFull(),
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

