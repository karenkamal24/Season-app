<?php

namespace App\Filament\Resources\ItemCategories\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ItemCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                Section::make($isArabic ? 'معلومات التصنيف' : 'Category Information')
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

                                TextEntry::make('icon')
                                    ->label($isArabic ? 'الأيقونة' : 'Icon')
                                    ->placeholder('-'),

                                TextEntry::make('icon_color')
                                    ->label($isArabic ? 'لون الأيقونة' : 'Icon Color')
                                    ->badge()
                                    ->color(fn($state) => $state ?? 'gray')
                                    ->placeholder('-'),

                                TextEntry::make('sort_order')
                                    ->label($isArabic ? 'ترتيب العرض' : 'Sort Order')
                                    ->placeholder('-'),

                                TextEntry::make('items_count')
                                    ->label($isArabic ? 'عدد الأغراض' : 'Items Count')
                                    ->formatStateUsing(fn($state) => $state ?? 0)
                                    ->placeholder('-'),

                                IconEntry::make('is_active')
                                    ->boolean()
                                    ->label($isArabic ? 'نشط' : 'Is Active'),
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

