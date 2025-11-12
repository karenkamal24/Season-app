<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                Section::make($isArabic ? 'معلومات الغرض' : 'Item Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('category.name_ar')
                                    ->label($isArabic ? 'التصنيف' : 'Category')
                                    ->formatStateUsing(fn($state, $record) => $state ? $state . ($record->category?->name_en ? ' (' . $record->category->name_en . ')' : '') : '-')
                                    ->badge()
                                    ->color('info')
                                    ->columnSpanFull(),

                                TextEntry::make('name_ar')
                                    ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name')
                                    ->placeholder('-')
                                    ->weight('bold'),

                                TextEntry::make('name_en')
                                    ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name')
                                    ->placeholder('-'),

                                TextEntry::make('default_weight')
                                    ->label($isArabic ? 'الوزن الافتراضي' : 'Default Weight')
                                    ->formatStateUsing(fn($state, $record) => $state ? $state . ' ' . ($record->weight_unit ?? 'kg') : '-')
                                    ->placeholder('-'),

                                TextEntry::make('weight_unit')
                                    ->label($isArabic ? 'وحدة الوزن' : 'Weight Unit')
                                    ->placeholder('-'),

                                TextEntry::make('icon')
                                    ->label($isArabic ? 'الأيقونة' : 'Icon')
                                    ->placeholder('-'),

                                TextEntry::make('sort_order')
                                    ->label($isArabic ? 'ترتيب العرض' : 'Sort Order')
                                    ->placeholder('-'),

                                IconEntry::make('is_active')
                                    ->boolean()
                                    ->label($isArabic ? 'نشط' : 'Is Active'),
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

