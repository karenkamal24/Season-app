<?php

namespace App\Filament\Resources\ItemCategories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ItemCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
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

                                TextEntry::make('icon')
                                    ->label('Icon')
                                    ->placeholder('-'),

                                TextEntry::make('icon_color')
                                    ->label('Icon Color')
                                    ->badge()
                                    ->color(fn($state) => $state ?? 'gray')
                                    ->placeholder('-'),

                                TextEntry::make('sort_order')
                                    ->label('Sort Order')
                                    ->placeholder('-'),

                                TextEntry::make('items_count')
                                    ->label('Items Count')
                                    ->formatStateUsing(fn($state) => $state ?? 0)
                                    ->placeholder('-'),

                                IconEntry::make('is_active')
                                    ->boolean()
                                    ->label('Is Active'),
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

