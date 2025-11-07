<?php

namespace App\Filament\Resources\BagTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class BagTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bag Type Information')
                    ->description('Enter the bag type details.')
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

                                TextInput::make('default_max_weight')
                                    ->label('Default Max Weight')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('kg')
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
                    ->description('Bag type descriptions in both languages.')
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

