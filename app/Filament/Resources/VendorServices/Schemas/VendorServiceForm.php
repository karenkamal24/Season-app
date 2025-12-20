<?php

namespace App\Filament\Resources\VendorServices\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;


class VendorServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات البائع' : 'Vendor Info')
                ->description($isArabic ? 'المعلومات الأساسية عن خدمة البائع' : 'Basic information about the vendor service')
                ->schema([
                    Select::make('user_id')
                        ->label($isArabic ? 'البائع' : 'Vendor')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('service_type_id')
                        ->label($isArabic ? 'نوع الخدمة' : 'Service Type')
                        ->relationship('serviceType', 'name_en')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->dehydrated(true),

                    TextInput::make('name')
                        ->label($isArabic ? 'اسم الخدمة' : 'Service Name')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label($isArabic ? 'الوصف' : 'Description')
                        ->placeholder($isArabic ? 'اكتب وصفاً مختصراً عن الخدمة...' : 'Write short description about the service...')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make($isArabic ? 'الاتصال والموقع' : 'Contact & Location')
                ->icon('heroicon-o-map-pin')
                ->schema([
                    TextInput::make('contact_number')
                        ->label($isArabic ? 'رقم الاتصال' : 'Contact Number')
                        ->tel(),

                    TextInput::make('address')
                        ->label($isArabic ? 'العنوان' : 'Address'),

                    TextInput::make('latitude')
                        ->numeric()
                        ->step('any')
                        ->label($isArabic ? 'خط العرض' : 'Latitude'),

                    TextInput::make('longitude')
                        ->numeric()
                        ->step('any')
                        ->label($isArabic ? 'خط الطول' : 'Longitude'),

                    Select::make('country_id')
                        ->label($isArabic ? 'الدولة' : 'Country')
                        ->relationship('country', $isArabic ? 'name_ar' : 'name_en')
                        ->searchable()
                        ->preload()
                        ->native(false),
                ])
                ->columns(2),

            Section::make($isArabic ? 'الملفات والحالة' : 'Files & Status')
                ->icon('heroicon-o-document')
                ->schema([
                    FileUpload::make('commercial_register')
                        ->label($isArabic ? 'السجل التجاري' : 'Commercial Register')
                        ->directory('vendor_services/registers')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable(),

                    FileUpload::make('images')
                        ->label($isArabic ? 'صور الخدمة' : 'Service Images')
                        ->directory('vendor_services/images')
                        ->disk('public')
                        ->visibility('public')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->downloadable()
                        ->imageEditor(),

                    Select::make('status')
                        ->label($isArabic ? 'الحالة' : 'Status')
                        ->options([
                            'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
                            'approved' => $isArabic ? 'موافق عليها' : 'Approved',
                            'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                            'disabled' => $isArabic ? 'معطلة' : 'Disabled',
                        ])
                        ->default('pending')
                        ->native(false)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }
}
