<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Item Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('category.name_ar')
                                    ->label('Category')
                                    ->formatStateUsing(fn($state, $record) => $state ? $state . ($record->category?->name_en ? ' (' . $record->category->name_en . ')' : '') : '-')
                                    ->badge()
                                    ->color('info')
                                    ->columnSpanFull(),

                                TextEntry::make('name_ar')
                                    ->label('الاسم(عربي)')
                                    ->placeholder('-')
                                    ->weight('bold'),

                                TextEntry::make('name_en')
                                    ->label('Name (English)')
                                    ->placeholder('-'),

                                TextEntry::make('default_weight')
                                    ->label('Default Weight')
                                    ->formatStateUsing(fn($state, $record) => $state ? $state . ' ' . ($record->weight_unit ?? 'kg') : '-')
                                    ->placeholder('-'),

                                TextEntry::make('weight_unit')
                                    ->label('Weight Unit')
                                    ->placeholder('-'),

                                TextEntry::make('icon')
                                    ->label('Icon')
                                    ->placeholder('-'),

                                TextEntry::make('sort_order')
                                    ->label('Sort Order')
                                    ->placeholder('-'),

                                IconEntry::make('is_active')
                                    ->boolean()
                                    ->label('Is Active'),
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

