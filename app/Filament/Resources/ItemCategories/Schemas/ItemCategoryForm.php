<?php

namespace App\Filament\Resources\ItemCategories\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ItemCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                Section::make($isArabic ? 'معلومات التصنيف' : 'Category Information')
                    ->description($isArabic ? 'أدخل تفاصيل التصنيف.' : 'Enter the category details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label($isArabic ? 'الاسم بالعربي' : 'Arabic Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('name_en')
                                    ->label($isArabic ? 'الاسم بالإنجليزي' : 'English Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('icon')
                                    ->label($isArabic ? 'الأيقونة' : 'Icon')
                                    ->maxLength(255)
                                    ->placeholder($isArabic ? 'مثال: heroicon-o-folder' : 'e.g., heroicon-o-folder')
                                    ->columnSpanFull(),

                                ColorPicker::make('icon_color')
                                    ->label($isArabic ? 'لون الأيقونة' : 'Icon Color')
                                    ->default('#000000'),

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
            ]);
    }
}

