<?php

namespace App\Filament\Resources\EmergencyNumbers\Tables;

use App\Helpers\LanguageHelper;
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
        $isArabic = LanguageHelper::isArabic();
        
        return $table
            ->columns([
                TextColumn::make('country.name_ar')
                    ->label($isArabic ? 'الدولة' : 'Country')
                    ->sortable()
                    ->searchable()
                    ->description(fn($record) => $record->country?->code)
                    ->badge()
                    ->color('info'),

                TextColumn::make('fire')
                    ->label($isArabic ? 'الإطفاء' : 'Fire Number')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-fire')
                    ->color('danger'),

                TextColumn::make('police')
                    ->label($isArabic ? 'الشرطة' : 'Police Number')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-shield-check')
                    ->color('warning'),

                TextColumn::make('ambulance')
                    ->label($isArabic ? 'الإسعاف' : 'Ambulance Number')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-truck')
                    ->color('success'),

                TextColumn::make('embassy')
                    ->label($isArabic ? 'السفارة' : 'Embassy Number')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-building-office')
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label($isArabic ? 'تاريخ الإنشاء' : 'Created')
                    ->dateTime('d M, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label($isArabic ? 'تاريخ التحديث' : 'Updated')
                    ->dateTime('d M, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
