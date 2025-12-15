<?php

namespace App\Filament\Resources\VendorServices\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;

class VendorServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'التفاصيل' : 'Details')
                ->description($isArabic ? 'تفاصيل خدمة البائع الرئيسية' : 'Main vendor service details')
                ->inlineLabel()
                ->components([
                    TextEntry::make('user.name')
                        ->label($isArabic ? 'البائع' : 'Vendor')
                        ->placeholder('-'),

                    TextEntry::make('user.email')
                        ->label($isArabic ? 'بريد البائع' : 'Vendor Email')
                        ->placeholder('-'),

                    TextEntry::make('user.phone')
                        ->label($isArabic ? 'هاتف البائع' : 'Vendor Phone')
                        ->placeholder('-'),

                    TextEntry::make('serviceType.name_en')
                        ->label($isArabic ? 'نوع الخدمة' : 'Service Type')
                        ->placeholder('-'),

                    TextEntry::make('name')
                        ->label($isArabic ? 'اسم الخدمة' : 'Service Name'),

                    TextEntry::make('description')
                        ->label($isArabic ? 'الوصف' : 'Description')
                        ->placeholder('-'),
                ]),

            Section::make($isArabic ? 'الاتصال والموقع' : 'Contact & Location')
                ->icon('heroicon-o-map-pin')
                ->inlineLabel()
                ->components([
                    TextEntry::make('contact_number')
                        ->label($isArabic ? 'رقم الاتصال' : 'Contact Number')
                        ->placeholder('-'),

                    TextEntry::make('address')
                        ->label($isArabic ? 'العنوان' : 'Address')
                        ->placeholder('-'),

                    TextEntry::make('latitude')
                        ->label($isArabic ? 'خط العرض' : 'Latitude')
                        ->placeholder('-'),

                    TextEntry::make('longitude')
                        ->label($isArabic ? 'خط الطول' : 'Longitude')
                        ->placeholder('-'),

                    TextEntry::make($isArabic ? 'country.name_ar' : 'country.name_en')
                        ->label($isArabic ? 'الدولة' : 'Country')
                        ->placeholder('-'),
                ]),

            Section::make($isArabic ? 'الملفات والحالة' : 'Files & Status')
                ->icon('heroicon-o-document')
                ->inlineLabel()
                ->components([
                    TextEntry::make('commercial_register')
                        ->label($isArabic ? 'السجل التجاري' : 'Commercial Register')
                        ->url(fn($record) => $record->commercial_register ? asset('storage/' . $record->commercial_register) : null)
                        ->openUrlInNewTab()
                        ->placeholder('-'),

                    ImageEntry::make('images')
                        ->label($isArabic ? 'صور الخدمة' : 'Service Images')
                        ->hiddenLabel()
                        ->limit(10),

                    TextEntry::make('status')
                        ->label($isArabic ? 'الحالة' : 'Status')
                        ->badge()
                        ->formatStateUsing(fn($state) => match($state) {
                            'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
                            'approved' => $isArabic ? 'موافق عليها' : 'Approved',
                            'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                            'disabled' => $isArabic ? 'معطلة' : 'Disabled',
                            default => $state,
                        }),

                    TextEntry::make('created_at')
                        ->label($isArabic ? 'تاريخ الإنشاء' : 'Created At')
                        ->dateTime(),

                    TextEntry::make('updated_at')
                        ->label($isArabic ? 'تاريخ التحديث' : 'Updated At')
                        ->dateTime(),
                ]),
        ]);
    }
}
