<?php

namespace App\Filament\Resources\BagTypes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class BagTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bag Type Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name_ar')
                                    ->label('الاسم(عربي)')
                                    ->placeholder('-')
                                    ->weight('bold'),

                                TextEntry::make('name_en')
                                    ->label('Name (English)')
                                    ->placeholder('-'),

                                TextEntry::make('default_max_weight')
                                    ->label('Default Max Weight')
                                    ->formatStateUsing(fn($state) => $state ? $state . ' kg' : '-')
                                    ->placeholder('-'),

                                IconEntry::make('is_active')
                                    ->boolean()
                                    ->label('Is Active'),

                                TextEntry::make('travelBags_count')
                                    ->label('Travel Bags Count')
                                    ->formatStateUsing(fn($state) => $state ?? 0)
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('Descriptions')
                    ->schema([
                        TextEntry::make('description_ar')
                            ->label('الوصف بالعربي')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('description_en')
                            ->label('Description (English)')
                            ->placeholder('-')
                            ->columnSpanFull(),
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

