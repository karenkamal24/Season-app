<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextEntry::make('name.en')
                    ->label('Name (English)')
                    ->placeholder('-'),

             
                TextEntry::make('name.ar')
                    ->label('الاسم (عربي)')
                    ->placeholder('-'),

                TextEntry::make('value')
                    ->label('Value')
                    ->numeric(),

                TextEntry::make('max')
                    ->label('Max')
                    ->numeric(),

                TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
