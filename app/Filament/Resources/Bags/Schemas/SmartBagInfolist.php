<?php

namespace App\Filament\Resources\Bags\Schemas;

use Filament\Infolists;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class SmartBagInfolist
{
    public static function getInfolist(): array
    {
        return [
            Section::make(__('bags.bag_details'))
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('name')
                            ->label(__('bags.bag_name')),

                        TextEntry::make('user.name')
                            ->label('User'),

                        TextEntry::make('trip_type')
                            ->label(__('bags.trip_type'))
                            ->formatStateUsing(fn ($state) => __("bags.trip_types.{$state}")),

                        TextEntry::make('duration')
                            ->label(__('bags.duration'))
                            ->suffix(' ' . __('bags.days')),

                        TextEntry::make('destination')
                            ->label(__('bags.destination')),

                        TextEntry::make('departure_date')
                            ->label(__('bags.departure_date'))
                            ->date(),

                        TextEntry::make('max_weight')
                            ->label(__('bags.max_weight'))
                            ->suffix(' ' . __('bags.kg')),

                        TextEntry::make('total_weight')
                            ->label(__('bags.total_weight'))
                            ->suffix(' ' . __('bags.kg')),

                        TextEntry::make('weight_percentage')
                            ->label(__('bags.weight_percentage'))
                            ->suffix('%')
                            ->color(fn ($state) => $state > 90 ? 'danger' : ($state > 70 ? 'warning' : 'success')),

                        TextEntry::make('remaining_weight')
                            ->label(__('bags.remaining_weight'))
                            ->suffix(' ' . __('bags.kg'))
                            ->color(fn ($state) => $state < 2 ? 'danger' : 'success'),

                        TextEntry::make('status')
                            ->label(__('bags.status'))
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'draft' => 'gray',
                                'in_progress' => 'info',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                            })
                            ->formatStateUsing(fn ($state) => __("bags.statuses.{$state}")),

                        TextEntry::make('days_until_departure')
                            ->label(__('bags.days_until_departure'))
                            ->suffix(' ' . __('bags.days'))
                            ->color(fn ($state) => $state < 3 ? 'danger' : ($state < 7 ? 'warning' : 'success')),

                        TextEntry::make('items_count')
                            ->label(__('bags.items'))
                            ->counts('items'),

                        TextEntry::make('is_analyzed')
                            ->label(__('bags.analyzed_at'))
                            ->badge()
                            ->color(fn ($state) => $state ? 'success' : 'gray')
                            ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
                    ]),

                    KeyValueEntry::make('preferences')
                        ->label(__('bags.preferences')),
                ]),

            Section::make(__('bags.items'))
                ->schema([
                    TextEntry::make('items')
                        ->label('')
                        ->listWithLineBreaks()
                        ->bulleted()
                        ->formatStateUsing(fn ($record) => 
                            $record->items->map(fn ($item) => 
                                "{$item->name} ({$item->weight} kg) - " . 
                                ($item->packed ? '✓' : '✗')
                            )->join("\n")
                        ),
                ])
                ->collapsible(),
        ];
    }
}

