<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema
            ->components([
                Section::make($isArabic ? 'المعلومات الشخصية' : 'Personal Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('photo_url')
                                    ->label($isArabic ? 'صورة الملف الشخصي' : 'Profile Picture')
                                    ->size(120)
                                    ->getStateUsing(
                                        fn($record) => $record->photo_url
                                            ? (str_starts_with($record->photo_url, 'http')
                                                ? $record->photo_url
                                                : asset('storage/' . $record->photo_url))
                                            : asset('images/default-avatar.png')
                                    )
                                    ->extraImgAttributes([
                                        'loading' => 'lazy',
                                        'class' => 'object-cover rounded-full shadow-md',
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label($isArabic ? 'الاسم الكامل' : 'Full Name')
                                            ->weight('bold')
                                            ->size('lg'),

                                        TextEntry::make('nickname')
                                            ->label($isArabic ? 'الاسم المستعار' : 'Nickname')
                                            ->placeholder('-'),

                                        TextEntry::make('email')
                                            ->label($isArabic ? 'البريد الإلكتروني' : 'Email Address')
                                            ->icon('heroicon-m-envelope')
                                            ->copyable(),

                                        TextEntry::make('phone')
                                            ->label($isArabic ? 'الهاتف' : 'Phone')
                                            ->icon('heroicon-m-phone')
                                            ->placeholder('-')
                                            ->copyable(),

                                        TextEntry::make('birth_date')
                                            ->label($isArabic ? 'تاريخ الميلاد' : 'Date of Birth')
                                            ->date()
                                            ->placeholder('-'),

                                        TextEntry::make('gender')
                                            ->label($isArabic ? 'الجنس' : 'Gender')
                                            ->badge()
                                            ->formatStateUsing(fn($state) => $state === 'male' ? ($isArabic ? 'ذكر' : 'Male') : ($isArabic ? 'أنثى' : 'Female'))
                                            ->color(fn(string $state): string => match ($state) {
                                                'male' => 'info',
                                                'female' => 'pink',
                                                default => 'gray',
                                            })
                                            ->placeholder('-'),
                                    ])
                                    ->columnSpan(2),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make($isArabic ? 'تفاصيل الحساب' : 'Account Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('role')
                                    ->label($isArabic ? 'دور المستخدم' : 'User Role')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => match($state) {
                                        'admin' => $isArabic ? 'مدير' : 'Admin',
                                        'vendor' => $isArabic ? 'بائع' : 'Vendor',
                                        'customer' => $isArabic ? 'عميل' : 'Customer',
                                        default => $state,
                                    })
                                    ->color(fn(string $state): string => match ($state) {
                                        'admin' => 'danger',
                                        'vendor' => 'warning',
                                        'customer' => 'success',
                                        default => 'gray',
                                    }),

                                TextEntry::make('is_blocked')
                                    ->label($isArabic ? 'الحالة' : 'Status')
                                    ->formatStateUsing(fn($state) => $state ? ($isArabic ? 'غير نشط' : 'Inactive') : ($isArabic ? 'نشط' : 'Active'))
                                    ->color(fn($state) => $state ? 'danger' : 'success'),

                                TextEntry::make('is_vendor')
                                    ->label($isArabic ? 'نوع الحساب' : 'Account Type')
                                    ->formatStateUsing(fn($state) => $state ? ($isArabic ? 'بائع' : 'Vendor') : ($isArabic ? 'مستخدم' : 'User'))
                                    ->color(fn($state) => $state ? 'danger' : 'success'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make($isArabic ? 'التواريخ' : 'Timestamps')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label($isArabic ? 'تاريخ إنشاء الحساب' : 'Account Created')
                                    ->dateTime()
                                    ->placeholder('-'),

                                TextEntry::make('updated_at')
                                    ->label($isArabic ? 'آخر تحديث' : 'Last Updated')
                                    ->dateTime()
                                    ->since()
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}
