<?php

namespace App\Filament\Resources\Items\Schemas;

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
        return $schema
            ->components([
                Section::make('Item Information')
                    ->description('Enter the item details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name_ar')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpanFull(),

                                TextInput::make('name_ar')
                                    ->label('الاسم بالعربي')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('name_en')
                                    ->label('English Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('default_weight')
                                    ->label('Default Weight')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('kg'),

                                Select::make('weight_unit')
                                    ->label('Weight Unit')
                                    ->options([
                                        'kg' => 'Kilogram (kg)',
                                        'g' => 'Gram (g)',
                                        'lb' => 'Pound (lb)',
                                        'oz' => 'Ounce (oz)',
                                    ])
                                    ->default('kg'),

                                TextInput::make('icon')
                                    ->label('Icon')
                                    ->maxLength(255)
                                    ->placeholder('e.g., heroicon-o-cube'),

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

                Section::make('Descriptions')
                    ->description('Item descriptions in both languages.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Textarea::make('description_ar')
                                    ->label('الوصف بالعربي')
                                    ->rows(3)
                                    ->maxLength(1000),

                                Textarea::make('description_en')
                                    ->label('Description (English)')
                                    ->rows(3)
                                    ->maxLength(1000),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

