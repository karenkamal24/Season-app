<?php

namespace App\Filament\Resources\GeographicalGuides\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class GeographicalGuideForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات المستخدم' : 'User Information')
                ->description($isArabic ? 'المستخدم المسؤول عن هذا الدليل' : 'User responsible for this guide')
                ->schema([
                    Select::make('user_id')
                        ->label($isArabic ? 'المستخدم' : 'User')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),

            Section::make($isArabic ? 'معلومات التصنيف' : 'Category Information')
                ->description($isArabic ? 'التصنيف والتصنيف الفرعي' : 'Category and sub category')
                ->schema([
                    Select::make('geographical_category_id')
                        ->label($isArabic ? 'التصنيف' : 'Category')
                        ->relationship('category', $isArabic ? 'name_ar' : 'name_en')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('geographical_sub_category_id', null)),

                    Select::make('geographical_sub_category_id')
                        ->label($isArabic ? 'التصنيف الفرعي' : 'Sub Category')
                        ->relationship('subCategory', $isArabic ? 'name_ar' : 'name_en', fn ($query, $get) =>
                            $query->where('geographical_category_id', $get('geographical_category_id'))
                        )
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])
                ->columns(2),

            Section::make($isArabic ? 'معلومات الخدمة' : 'Service Information')
                ->description($isArabic ? 'معلومات الخدمة الأساسية' : 'Basic service information')
                ->schema([
                    TextInput::make('service_name')
                        ->label($isArabic ? 'اسم الخدمة' : 'Service Name')
                        ->required()
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label($isArabic ? 'الوصف' : 'Description')
                        ->rows(3)
                        ->maxLength(500),
                ])
                ->columns(1),

            Section::make($isArabic ? 'معلومات الاتصال' : 'Contact Information')
                ->description($isArabic ? 'أرقام الهواتف ووسائل الاتصال' : 'Phone numbers and contact methods')
                ->schema([
                    TextInput::make('phone_1')
                        ->label($isArabic ? 'رقم الموبايل الأول' : 'Phone Number 1')
                        ->tel()
                        ->maxLength(20),

                    TextInput::make('phone_2')
                        ->label($isArabic ? 'رقم الموبايل الثاني' : 'Phone Number 2')
                        ->tel()
                        ->maxLength(20),

                    TextInput::make('website')
                        ->label($isArabic ? 'الموقع الإلكتروني' : 'Website')
                        ->url()
                        ->maxLength(255),
                ])
                ->columns(3),

            Section::make($isArabic ? 'الموقع الجغرافي' : 'Geographical Location')
                ->description($isArabic ? 'الدولة والمدينة والعنوان' : 'Country, city and address')
                ->schema([
                    Select::make('country_id')
                        ->label($isArabic ? 'الدولة' : 'Country')
                        ->relationship('country', $isArabic ? 'name_ar' : 'name_en')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('city_id', null)),

                    Select::make('city_id')
                        ->label($isArabic ? 'المدينة' : 'City')
                        ->relationship('city', $isArabic ? 'name_ar' : 'name_en', fn ($query, $get) =>
                            $query->where('country_id', $get('country_id'))
                        )
                        ->searchable()
                        ->preload()
                        ->required(),

                    Textarea::make('address')
                        ->label($isArabic ? 'العنوان' : 'Address')
                        ->rows(2)
                        ->maxLength(500),
                ])
                ->columns(1),

            Section::make($isArabic ? 'الإحداثيات الجغرافية' : 'Geographical Coordinates')
                ->description($isArabic ? 'خطوط الطول والعرض (Latitude & Longitude)' : 'Latitude and longitude')
                ->schema([
                    TextInput::make('latitude')
                        ->label($isArabic ? 'خط العرض (Latitude)' : 'Latitude')
                        ->numeric()
                        ->step(0.00000001)
                        ->prefix('°')
                        ->minValue(-90)
                        ->maxValue(90),

                    TextInput::make('longitude')
                        ->label($isArabic ? 'خط الطول (Longitude)' : 'Longitude')
                        ->numeric()
                        ->step(0.00000001)
                        ->prefix('°')
                        ->minValue(-180)
                        ->maxValue(180),
                ])
                ->columns(2),

            Section::make($isArabic ? 'معلومات إضافية' : 'Additional Information')
                ->schema([
                    TextInput::make('commercial_register')
                        ->label($isArabic ? 'السجل التجاري' : 'Commercial Register')
                        ->maxLength(255),

                    Toggle::make('is_active')
                        ->label($isArabic ? 'نشط' : 'Is Active')
                        ->default(true)
                        ->required(),

                    Select::make('status')
                        ->label($isArabic ? 'الحالة' : 'Status')
                        ->options([
                            'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
                            'approved' => $isArabic ? 'موافق عليها' : 'Approved',
                            'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                        ])
                        ->default('pending')
                        ->required()
                        ->native(false),
                ])
                ->columns(3),
        ]);
    }
}

