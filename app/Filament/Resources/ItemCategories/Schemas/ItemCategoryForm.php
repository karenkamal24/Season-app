<?php

namespace App\Filament\Resources\ItemCategories\Schemas;

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
        return $schema
            ->components([
                Section::make('Category Information')
                    ->description('Enter the category details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label('الاسم بالعربي')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('name_en')
                                    ->label('English Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('icon')
                                    ->label('Icon')
                                    ->maxLength(255)
                                    ->placeholder('e.g., heroicon-o-folder')
                                    ->columnSpanFull(),

                                ColorPicker::make('icon_color')
                                    ->label('Icon Color')
                                    ->default('#000000'),

                                TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label('Is Active')
                                    ->default(true)
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

