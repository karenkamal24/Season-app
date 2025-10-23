<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('nickname')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')->label('Email address')->searchable(),
                TextColumn::make('phone')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('birth_date')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gender')->badge()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('photo_url')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_blocked')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => $state ? 'Unactive' : 'Active')
                    ->color(fn($state) => $state ? 'gray' : 'success'),
                TextColumn::make('coins')->numeric()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_vendor')
                    ->label('Account Type')
                    ->formatStateUsing(fn($state) => $state ? 'Vendor' : 'User')
                    ->color(fn($state) => $state ? 'success' : 'gray'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc'); 
    }
}
