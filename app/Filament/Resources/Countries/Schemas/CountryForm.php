<?php

namespace App\Filament\Resources\Countries\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CountryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Country Information')
                    ->description('Enter the country details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label('Arabic Name')
                                    ->required()
                                    ->maxLength(255),

                                // TextInput::make('name_en')
                                //     ->label('English Name')
                                //     ->maxLength(255),

                                TextInput::make('code')
                                    ->label('Country Code')
                                    ->required()
                                    ->maxLength(3)
                                    ->unique(ignoreRecord: true)
                                     ->afterStateUpdated(fn ($state, callable $set) => $set('code', strtoupper($state)))
                                    ->placeholder('e.g., EGY, SAU'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
