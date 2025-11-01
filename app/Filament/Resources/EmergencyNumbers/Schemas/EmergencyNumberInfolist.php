<?php

namespace App\Filament\Resources\EmergencyNumbers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class EmergencyNumberInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Emergency Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('country.name_ar')
                                    ->label('Country')
                                    ->placeholder('-')
                                    ->badge()
                                    ->color('info')
                                    ->columnSpan(1),

                                TextEntry::make('country.code')
                                    ->label('Country Code')
                                    ->placeholder('-')
                                    ->badge()
                                    ->color('success')
                                    ->columnSpan(1),

                                TextEntry::make('fire')
                                    ->label('Fire Number')
                                    ->icon('heroicon-o-fire')
                                    ->placeholder('-'),

                                TextEntry::make('police')
                                    ->label('Police Number')
                                    ->icon('heroicon-o-shield-check')
                                    ->placeholder('-'),

                                TextEntry::make('ambulance')
                                    ->label('Ambulance Number')
                                    ->icon('heroicon-m-heart')
                                    ->placeholder('-'),

                                TextEntry::make('embassy')
                                    ->label('Embassy Number')
                                    ->icon('heroicon-o-building-office')
                                    ->placeholder('-'),
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
