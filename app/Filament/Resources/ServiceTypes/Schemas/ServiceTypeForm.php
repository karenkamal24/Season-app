<?php

namespace App\Filament\Resources\ServiceTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name_ar')
                    ->label('الاسم (عربي) ')
                    ->required(),
                TextInput::make('name_en')
                    ->label('Name (English)')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
