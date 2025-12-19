<?php

namespace App\Filament\Resources\GeographicalGuides\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class GeographicalGuideInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات المستخدم' : 'User Information')
                ->inlineLabel()
                ->components([
                    TextEntry::make('user.name')
                        ->label($isArabic ? 'اسم المستخدم' : 'User Name')
                        ->placeholder('-'),

                    TextEntry::make('user.email')
                        ->label($isArabic ? 'البريد الإلكتروني' : 'Email')
                        ->placeholder('-'),
                ]),

            Section::make($isArabic ? 'معلومات التصنيف' : 'Category Information')
                ->inlineLabel()
                ->components([
                    TextEntry::make('category.name_ar')
                        ->label($isArabic ? 'التصنيف (عربي)' : 'Category (Arabic)')
                        ->placeholder('-'),

                    TextEntry::make('category.name_en')
                        ->label($isArabic ? 'التصنيف (إنجليزي)' : 'Category (English)')
                        ->placeholder('-'),

                    TextEntry::make('subCategory.name_ar')
                        ->label($isArabic ? 'التصنيف الفرعي (عربي)' : 'Sub Category (Arabic)')
                        ->placeholder('-'),

                    TextEntry::make('subCategory.name_en')
                        ->label($isArabic ? 'التصنيف الفرعي (إنجليزي)' : 'Sub Category (English)')
                        ->placeholder('-'),
                ]),

            Section::make($isArabic ? 'معلومات الخدمة' : 'Service Information')
                ->inlineLabel()
                ->components([
                    TextEntry::make('service_name')
                        ->label($isArabic ? 'اسم الخدمة' : 'Service Name')
                        ->placeholder('-'),

                    TextEntry::make('description')
                        ->label($isArabic ? 'الوصف' : 'Description')
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),

            Section::make($isArabic ? 'معلومات الاتصال' : 'Contact Information')
                ->inlineLabel()
                ->components([
                    TextEntry::make('phone_1')
                        ->label($isArabic ? 'رقم الموبايل الأول' : 'Phone Number 1')
                        ->placeholder('-'),

                    TextEntry::make('phone_2')
                        ->label($isArabic ? 'رقم الموبايل الثاني' : 'Phone Number 2')
                        ->placeholder('-'),

                    TextEntry::make('website')
                        ->label($isArabic ? 'الموقع الإلكتروني' : 'Website')
                        ->url(fn($record) => $record->website)
                        ->openUrlInNewTab()
                        ->placeholder('-'),
                ]),

            Section::make($isArabic ? 'الموقع الجغرافي' : 'Geographical Location')
                ->inlineLabel()
                ->components([
                    TextEntry::make('country.name_ar')
                        ->label($isArabic ? 'الدولة (عربي)' : 'Country (Arabic)')
                        ->placeholder('-'),

                    TextEntry::make('country.name_en')
                        ->label($isArabic ? 'الدولة (إنجليزي)' : 'Country (English)')
                        ->placeholder('-'),

                    TextEntry::make('city.name_ar')
                        ->label($isArabic ? 'المدينة (عربي)' : 'City (Arabic)')
                        ->placeholder('-'),

                    TextEntry::make('city.name_en')
                        ->label($isArabic ? 'المدينة (إنجليزي)' : 'City (English)')
                        ->placeholder('-'),

                    TextEntry::make('address')
                        ->label($isArabic ? 'العنوان' : 'Address')
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),

            Section::make($isArabic ? 'الإحداثيات الجغرافية' : 'Geographical Coordinates')
                ->inlineLabel()
                ->components([
                    TextEntry::make('latitude')
                        ->label($isArabic ? 'خط العرض (Latitude)' : 'Latitude')
                        ->placeholder('-')
                        ->suffix('°'),

                    TextEntry::make('longitude')
                        ->label($isArabic ? 'خط الطول (Longitude)' : 'Longitude')
                        ->placeholder('-')
                        ->suffix('°'),

                    TextEntry::make('location_map')
                        ->label($isArabic ? 'عرض الموقع على الخريطة' : 'View Location on Map')
                        ->formatStateUsing(function ($record) use ($isArabic) {
                            if ($record->latitude && $record->longitude) {
                                return $isArabic
                                    ? 'افتح على Google Maps'
                                    : 'Open on Google Maps';
                            }
                            return '-';
                        })
                        ->url(function ($record) {
                            if ($record->latitude && $record->longitude) {
                                return "https://www.google.com/maps?q={$record->latitude},{$record->longitude}";
                            }
                            return null;
                        })
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-map-pin')
                        ->color('primary')
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),

            Section::make($isArabic ? 'معلومات إضافية' : 'Additional Information')
                ->inlineLabel()
                ->components([
                    TextEntry::make('commercial_register')
                        ->label($isArabic ? 'السجل التجاري' : 'Commercial Register')
                        ->formatStateUsing(function ($record) use ($isArabic) {
                            if ($record->commercial_register) {
                                return $isArabic
                                    ? 'تحميل السجل التجاري'
                                    : 'Download Commercial Register';
                            }
                            return '-';
                        })
                        ->url(function ($record) {
                            if ($record->commercial_register) {
                                if (str_starts_with($record->commercial_register, 'http')) {
                                    return $record->commercial_register;
                                }
                                return asset('storage/' . $record->commercial_register);
                            }
                            return null;
                        })
                        ->openUrlInNewTab()
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->placeholder('-'),

                    IconEntry::make('is_active')
                        ->boolean()
                        ->label($isArabic ? 'نشط' : 'Is Active'),
                ]),

            Section::make($isArabic ? 'التواريخ' : 'Dates')
                ->icon('heroicon-o-calendar')
                ->inlineLabel()
                ->components([
                    TextEntry::make('created_at')
                        ->label($isArabic ? 'تاريخ الإنشاء' : 'Created At')
                        ->dateTime()
                        ->placeholder('-'),

                    TextEntry::make('updated_at')
                        ->label($isArabic ? 'تاريخ التحديث' : 'Updated At')
                        ->dateTime()
                        ->placeholder('-'),
                ]),
        ]);
    }
}

