<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\FusedGroup;
use Filament\Forms\Components\TextInput;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                FusedGroup::make([
                    TextInput::make('name.en')
                        ->label('Name (English)')
                        ->placeholder('Enter English name')
                        ->required(),

                    TextInput::make('name.ar')
                        ->label('Name (Arabic)')
                        ->placeholder('ادخل الاسم بالعربية')
                        ->required(),
                ])
                    ->label('Setting Name')
                    ->columns(2),

                FusedGroup::make([
                    TextInput::make('value')
                        ->label('Value')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->suffix('points'),

                    TextInput::make('max')
                        ->label('Max Points')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->suffix('points')
                        ->helperText('Maximum number of points allowed.'),
                ])
                    ->label('Points Configuration')
                    ->columns(2),
            ]);
    }
}
