<?php

namespace App\Filament\Resources\Bags\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SmartBagsTable
{
    public static function getTable(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label(__('bags.bag_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('trip_type')
                    ->label(__('bags.trip_type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => __("bags.trip_types.{$state}"))
                    ->sortable(),

                TextColumn::make('destination')
                    ->label(__('bags.destination'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('departure_date')
                    ->label(__('bags.departure_date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('total_weight')
                    ->label(__('bags.total_weight'))
                    ->suffix(' ' . __('bags.kg'))
                    ->sortable()
                    ->color(fn ($record) => $record->is_overweight ? 'danger' : 'success'),

                TextColumn::make('weight_percentage')
                    ->label(__('bags.weight_percentage'))
                    ->suffix('%')
                    ->sortable()
                    ->color(fn ($state) => $state > 90 ? 'danger' : ($state > 70 ? 'warning' : 'success')),

                TextColumn::make('status')
                    ->label(__('bags.status'))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'draft' => 'gray',
                        'in_progress' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn ($state) => __("bags.statuses.{$state}"))
                    ->sortable(),

                TextColumn::make('is_analyzed')
                    ->label('Analyzed')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? '✓' : '✗')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('trip_type')
                    ->label(__('bags.trip_type'))
                    ->options([
                        'عمل' => __('bags.trip_types.عمل'),
                        'سياحة' => __('bags.trip_types.سياحة'),
                        'عائلية' => __('bags.trip_types.عائلية'),
                        'علاج' => __('bags.trip_types.علاج'),
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label(__('bags.status'))
                    ->options([
                        'draft' => __('bags.statuses.draft'),
                        'in_progress' => __('bags.statuses.in_progress'),
                        'completed' => __('bags.statuses.completed'),
                        'cancelled' => __('bags.statuses.cancelled'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_analyzed')
                    ->label('Analyzed'),

                Tables\Filters\TernaryFilter::make('is_overweight')
                    ->label('Overweight')
                    ->queries(
                        true: fn ($query) => $query->whereRaw('total_weight > max_weight'),
                        false: fn ($query) => $query->whereRaw('total_weight <= max_weight'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('departure_date', 'desc');
    }
}

