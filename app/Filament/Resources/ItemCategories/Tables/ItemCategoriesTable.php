<?php

namespace App\Filament\Resources\ItemCategories\Tables;

use App\Helpers\LanguageHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class ItemCategoriesTable
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

                ImageColumn::make('icon')
                    ->label($isArabic ? 'الأيقونة' : 'Icon')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl(url('/images/default-icon.png'))
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('icon_color')
                    ->label($isArabic ? 'لون الأيقونة' : 'Icon Color')
                    ->badge()
                    ->color(fn($state) => $state ?? 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sort_order')
                    ->label($isArabic ? 'ترتيب العرض' : 'Sort Order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('items_count')
                    ->label($isArabic ? 'عدد الأغراض' : 'Items Count')
                    ->counts('items')
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

