<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema
            ->components([
                Section::make($isArabic ? 'معلومات الغرض' : 'Item Information')
                    ->description($isArabic ? 'أدخل تفاصيل الغرض.' : 'Enter the item details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category_id')
                                    ->label($isArabic ? 'التصنيف' : 'Category')
                                    ->relationship('category', 'name_ar')
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
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('default_weight')
                                    ->label($isArabic ? 'الوزن الافتراضي' : 'Default Weight')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix(fn ($get) => $get('weight_unit') ?? 'kg')
                                    ->reactive(),

                                Select::make('weight_unit')
                                    ->label($isArabic ? 'وحدة الوزن' : 'Weight Unit')
                                    ->options([
                                        'kg' => $isArabic ? 'كيلوجرام (kg)' : 'Kilogram (kg)',
                                        'g' => $isArabic ? 'جرام (g)' : 'Gram (g)',
                                        'lb' => $isArabic ? 'رطل (lb)' : 'Pound (lb)',
                                        'oz' => $isArabic ? 'أونصة (oz)' : 'Ounce (oz)',
                                    ])
                                    ->default('kg')
                                    ->reactive(),

                                TextInput::make('sort_order')
                                    ->label($isArabic ? 'ترتيب العرض' : 'Sort Order')
                                    ->numeric()
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label($isArabic ? 'نشط' : 'Is Active')
                                    ->default(true)
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make($isArabic ? 'الأوصاف' : 'Descriptions')
                    ->description($isArabic ? 'أوصاف الغرض باللغتين.' : 'Item descriptions in both languages.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Textarea::make('description_ar')
                                    ->label($isArabic ? 'الوصف بالعربي' : 'Arabic Description')
                                    ->rows(3)
                                    ->maxLength(1000),

                                Textarea::make('description_en')
                                    ->label($isArabic ? 'الوصف بالإنجليزي' : 'English Description')
                                    ->rows(3)
                                    ->maxLength(1000),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

