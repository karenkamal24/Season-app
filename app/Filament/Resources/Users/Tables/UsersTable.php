<?php

namespace App\Filament\Resources\Users\Tables;

use App\Helpers\LanguageHelper;
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
        $isArabic = LanguageHelper::isArabic();
        
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label($isArabic ? 'الاسم' : 'Name')
                    ->searchable(),
                
                TextColumn::make('nickname')
                    ->label($isArabic ? 'الاسم المستعار' : 'Nickname')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('email')
                    ->label($isArabic ? 'البريد الإلكتروني' : 'Email Address')
                    ->searchable(),
                
                TextColumn::make('phone')
                    ->label($isArabic ? 'الهاتف' : 'Phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('birth_date')
                    ->label($isArabic ? 'تاريخ الميلاد' : 'Date of Birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('gender')
                    ->label($isArabic ? 'الجنس' : 'Gender')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state === 'male' ? ($isArabic ? 'ذكر' : 'Male') : ($isArabic ? 'أنثى' : 'Female'))
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('photo_url')
                    ->label($isArabic ? 'الصورة' : 'Photo')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('address')
                    ->label($isArabic ? 'العنوان' : 'Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('role')
                    ->label($isArabic ? 'الدور' : 'Role')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'admin' => $isArabic ? 'مدير' : 'Admin',
                        'vendor' => $isArabic ? 'بائع' : 'Vendor',
                        'customer' => $isArabic ? 'عميل' : 'Customer',
                        default => $state,
                    })
                    ->color(fn($state) => match($state) {
                        'admin' => 'danger',
                        'vendor' => 'warning',
                        'customer' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('is_blocked')
                    ->label($isArabic ? 'الحالة' : 'Status')
                    ->formatStateUsing(fn($state) => $state ? ($isArabic ? 'غير نشط' : 'Inactive') : ($isArabic ? 'نشط' : 'Active'))
                    ->color(fn($state) => $state ? 'gray' : 'success'),
                
                TextColumn::make('coins')
                    ->label($isArabic ? 'النقاط' : 'Coins')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
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
                
                TextColumn::make('is_vendor')
                    ->label($isArabic ? 'نوع الحساب' : 'Account Type')
                    ->formatStateUsing(fn($state) => $state ? ($isArabic ? 'بائع' : 'Vendor') : ($isArabic ? 'مستخدم' : 'User'))
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
