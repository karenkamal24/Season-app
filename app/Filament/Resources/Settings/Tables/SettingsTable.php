<?php

namespace App\Filament\Resources\Settings\Tables;

use App\Helpers\LanguageHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $table
            ->columns([
                TextColumn::make('name.ar')
                    ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('name.en')
                    ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('value')
                    ->label($isArabic ? 'القيمة' : 'Value')
                    ->numeric()
                    ->sortable(),
                
                TextColumn::make('max')
                    ->label($isArabic ? 'الحد الأقصى' : 'Max')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label($isArabic ? 'تاريخ الإنشاء' : 'Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label($isArabic ? 'تاريخ التحديث' : 'Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
