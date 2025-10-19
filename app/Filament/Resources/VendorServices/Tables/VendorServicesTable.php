<?php

namespace App\Filament\Resources\VendorServices\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Select;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;

class VendorServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Vendor')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('serviceType.name_en')
                    ->label('Service Type')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-briefcase'),

                TextColumn::make('name')
                    ->label('Service Name')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-building-storefront'),

                TextColumn::make('contact_number')
                    ->label('Phone')
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->searchable(),

                TextColumn::make('address')
                    ->label('Address')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state)
                    ->icon('heroicon-o-map-pin')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('commercial_register')
                    ->label('Commercial File')
                    ->boolean()
                    ->tooltip('Has uploaded commercial register'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'disabled' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        'disabled' => 'heroicon-o-pause-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->label('Status')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'disabled' => 'Disabled',
                    ]),
            ])

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info')
                        ->icon('heroicon-o-eye'),

                    EditAction::make()
                        ->color('primary')
                        ->icon('heroicon-o-pencil-square'),

                    Action::make('change_status')
                        ->label('Change Status')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->form([
                            Select::make('status')
                                ->label('Select new status')
                                ->options([
                                    'pending' => 'Pending',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    'disabled' => 'Disabled',
                                ])
                                ->required(),
                        ])
                        ->action(function ($record, array $data): void {
                            $record->update(['status' => $data['status']]);
                        })
                        ->successNotificationTitle('Status updated successfully!'),

                    DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ]),
            ]);



    }
}
