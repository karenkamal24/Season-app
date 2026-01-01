<?php

namespace App\Filament\Resources\Bags\Schemas;

use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SmartBagForm
{
    public static function getForm(): array
    {
        return [
            Section::make(__('bags.bag_details'))
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label(__('bags.bag_name'))
                            ->required()
                            ->maxLength(255),

                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('trip_type')
                            ->label(__('bags.trip_type'))
                            ->options([
                                'عمل' => __('bags.trip_types.عمل'),
                                'سياحة' => __('bags.trip_types.سياحة'),
                                'عائلية' => __('bags.trip_types.عائلية'),
                                'علاج' => __('bags.trip_types.علاج'),
                            ])
                            ->required(),

                        TextInput::make('duration')
                            ->label(__('bags.duration'))
                            ->numeric()
                            ->required()
                            ->suffix(__('bags.days'))
                            ->minValue(1),

                        TextInput::make('destination')
                            ->label(__('bags.destination'))
                            ->required()
                            ->maxLength(255),

                        DatePicker::make('departure_date')
                            ->label(__('bags.departure_date'))
                            ->required()
                            ->native(false)
                            ->displayFormat('Y-m-d'),

                        TextInput::make('max_weight')
                            ->label(__('bags.max_weight'))
                            ->numeric()
                            ->required()
                            ->suffix(__('bags.kg'))
                            ->default(20.00),

                        TextInput::make('total_weight')
                            ->label(__('bags.total_weight'))
                            ->numeric()
                            ->disabled()
                            ->suffix(__('bags.kg'))
                            ->default(0),

                        Select::make('status')
                            ->label(__('bags.status'))
                            ->options([
                                'draft' => __('bags.statuses.draft'),
                                'in_progress' => __('bags.statuses.in_progress'),
                                'completed' => __('bags.statuses.completed'),
                                'cancelled' => __('bags.statuses.cancelled'),
                            ])
                            ->default('draft')
                            ->required(),
                    ]),

                    KeyValue::make('preferences')
                        ->label(__('bags.preferences'))
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                        ->reorderable(),
                ]),
        ];
    }
}

