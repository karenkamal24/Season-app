<?php

namespace App\Filament\Resources\ServiceTypes\Tables;

use App\Helpers\LanguageHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceTypesTable
{
    public static function configure(Table $table): Table
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $table
            ->columns([
                TextColumn::make('name_ar')
                    ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('name_en')
                    ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name')
                    ->searchable()
                    ->sortable(),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->label($isArabic ? 'نشط' : 'Active'),
                
                TextColumn::make('created_at')
                    ->label($isArabic ? 'تاريخ الإنشاء' : 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label($isArabic ? 'تاريخ التحديث' : 'Updated At')
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
