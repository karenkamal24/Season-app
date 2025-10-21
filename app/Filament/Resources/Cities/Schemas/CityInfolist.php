<?php

namespace App\Filament\Resources\Cities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class CityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('City Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('country.name_ar')
                                    ->label('Country')
                                    ->placeholder('-')
                                    ->badge()
                                    ->color('success')
                                    ->columnSpanFull(),

                                TextEntry::make('name_ar')
                                    ->label('Arabic Name')
                                    ->placeholder('-')
                                    ->weight('bold'),

                                // TextEntry::make('name_en')
                                //     ->label('English Name')
                                //     ->placeholder('-'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Timestamps')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime()
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}
