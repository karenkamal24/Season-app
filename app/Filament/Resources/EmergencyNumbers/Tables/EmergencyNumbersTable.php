<?php

namespace App\Filament\Resources\EmergencyNumbers\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

class EmergencyNumbersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('country.name_ar')
                    ->label('Country')
                    ->sortable()
                    ->searchable()
                    ->description(fn($record) => $record->country?->code)
                    ->badge()
                    ->color('info'),

                TextColumn::make('fire')
                    ->label('Fire Number')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-fire')
                    ->color('danger'),

                TextColumn::make('police')
                    ->label('Police Number')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-shield-check')
                    ->color('warning'),

                TextColumn::make('ambulance')
                    ->label('Ambulance Number')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-truck')
                    ->color('success'),

                TextColumn::make('embassy')
                    ->label('Embassy Number')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-building-office')
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Add filters later if needed
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square'),

                DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->color('danger'),
                ]),
            ]);
    }
}
