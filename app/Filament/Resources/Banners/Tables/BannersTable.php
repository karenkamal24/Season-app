<?php

namespace App\Filament\Resources\Banners\Tables;

use App\Helpers\LanguageHelper;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        $isArabic = LanguageHelper::isArabic();

        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label($isArabic ? 'الصورة' : 'Image')
                    ->getStateUsing(function ($record) {
                        if (!$record->image) {
                            return null;
                        }

                        if (str_starts_with($record->image, 'http')) {
                            return $record->image;
                        }

                        return asset('storage/' . $record->image);
                    })
                    ->circular()
                    ->size(50),

                TextColumn::make('language')
                    ->label($isArabic ? 'اللغة' : 'Language')
                    ->formatStateUsing(fn($state) => $state === 'ar'
                        ? ($isArabic ? 'العربية' : 'Arabic')
                        : ($isArabic ? 'الإنجليزية' : 'English'))
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('link')
                    ->label($isArabic ? 'الرابط' : 'Link')
                    ->url(fn($record) => $record->link)
                    ->openUrlInNewTab()
                    ->limit(30)
                    ->toggleable()
                    ->placeholder('-'),

                IconColumn::make('is_active')
                    ->label($isArabic ? 'نشط' : 'Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label($isArabic ? 'تاريخ الإنشاء' : 'Created At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label($isArabic ? 'تاريخ التحديث' : 'Updated At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label($isArabic ? 'نشط' : 'Active')
                    ->placeholder($isArabic ? 'الكل' : 'All')
                    ->trueLabel($isArabic ? 'نشط' : 'Active')
                    ->falseLabel($isArabic ? 'غير نشط' : 'Inactive'),

                Tables\Filters\SelectFilter::make('language')
                    ->label($isArabic ? 'اللغة' : 'Language')
                    ->options([
                        'en' => $isArabic ? 'الإنجليزية' : 'English',
                        'ar' => $isArabic ? 'العربية' : 'Arabic',
                    ]),
            ])

            ->defaultSort('created_at', 'desc')

            ->recordActions([
                ViewAction::make()
                    ->color('info')
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->color('primary')
                    ->icon('heroicon-o-pencil-square'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ]);
    }
}

