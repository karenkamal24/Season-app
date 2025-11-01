<?php

namespace App\Filament\Resources\EmergencyNumbers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class EmergencyNumberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Emergency Information')
                ->description('Enter the emergency numbers for each country.')
                ->columnSpanFull()
                ->schema([

                    Grid::make(1)->schema([
                        Select::make('country_id')
                            ->label('Country')
                            ->relationship('country', 'name_ar')
                            ->searchable()
                            ->required()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name_ar} ({$record->code})")
                            ->columnSpanFull(),

                        Fieldset::make('Emergency Numbers')->schema([
                            TextInput::make('fire')
                                ->label('Fire Number')
                                ->prefixIcon('heroicon-o-fire')
                                ->tel()
                                ->required(),

                            TextInput::make('police')
                                ->label('Police Number')
                                ->prefixIcon('heroicon-o-shield-check')
                                ->tel()
                                ->required(),

                            TextInput::make('ambulance')
                                ->label('Ambulance Number')
                                ->prefixIcon('heroicon-o-truck')
                                ->tel()
                                ->required(),

                            TextInput::make('embassy')
                                ->label('Embassy Number')
                                ->prefixIcon('heroicon-o-building-office')
                                ->tel()
                                ->required(),
                        ])->columns(2),
                    ]),
                ]),
        ]);
    }
}
