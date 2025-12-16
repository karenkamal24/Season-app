<?php

namespace App\Filament\Resources\GeographicalGuides\Tables;

use App\Helpers\LanguageHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;

class GeographicalGuidesTable
{
    public static function configure(Table $table): Table
    {
        $isArabic = LanguageHelper::isArabic();

        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label($isArabic ? 'المستخدم' : 'User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('service_name')
                    ->label($isArabic ? 'اسم الخدمة' : 'Service Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name_ar')
                    ->label($isArabic ? 'التصنيف' : 'Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subCategory.name_ar')
                    ->label($isArabic ? 'التصنيف الفرعي' : 'Sub Category')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('country.name_ar')
                    ->label($isArabic ? 'الدولة' : 'Country')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city.name_ar')
                    ->label($isArabic ? 'المدينة' : 'City')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone_1')
                    ->label($isArabic ? 'رقم الموبايل 1' : 'Phone 1')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone_2')
                    ->label($isArabic ? 'رقم الموبايل 2' : 'Phone 2')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label($isArabic ? 'نشط' : 'Active'),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
                        'approved' => $isArabic ? 'موافق عليها' : 'Approved',
                        'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->label($isArabic ? 'الحالة' : 'Status')
                    ->sortable(),

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
                Tables\Filters\SelectFilter::make('status')
                    ->label($isArabic ? 'الحالة' : 'Status')
                    ->options([
                        'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
                        'approved' => $isArabic ? 'موافق عليها' : 'Approved',
                        'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('change_status')
                    ->label($isArabic ? 'تغيير الحالة' : 'Change Status')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->form([
                        Select::make('status')
                            ->label($isArabic ? 'اختر الحالة الجديدة' : 'Select new status')
                            ->options([
                                'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
                                'approved' => $isArabic ? 'موافق عليها' : 'Approved',
                                'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                            ])
                            ->required()
                            ->default(fn ($record) => $record->status),
                    ])
                    ->action(function ($record, array $data) use ($isArabic): void {
                        $record->update(['status' => $data['status']]);
                    })
                    ->successNotificationTitle($isArabic ? 'تم تحديث الحالة بنجاح!' : 'Status updated successfully!'),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

