<?php

namespace App\Filament\Resources\CategoryApps\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CategoryAppForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات التطبيق' : 'App Information')
                ->description($isArabic ? 'المعلومات الأساسية عن التطبيق' : 'Basic information about the app')
                ->schema([
                    Select::make('category_id')
                        ->label($isArabic ? 'التصنيف' : 'Category')
                        ->relationship('category', $isArabic ? 'name_ar' : 'name_en')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('countries')
                        ->label($isArabic ? 'الدول' : 'Countries')
                        ->relationship('countries', $isArabic ? 'name_ar' : 'name_en')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('name_ar')
                        ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name')
                        ->maxLength(255),

                    TextInput::make('name_en')
                        ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name')
                        ->maxLength(255),

                    Toggle::make('is_active')
                        ->label($isArabic ? 'نشط' : 'Is Active')
                        ->default(true)
                        ->required(),
                ])
                ->columns(2),

            Section::make($isArabic ? 'الصورة والرابط' : 'Icon & URL')
                ->icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make('icon')
                        ->label($isArabic ? 'أيقونة التطبيق' : 'App Icon')
                        ->disk('public')
                        ->directory('category_apps/icons')
                        ->image()
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->imageEditor()
                        ->helperText($isArabic ? 'صورة أيقونة التطبيق' : 'App icon image'),

                    TextInput::make('url')
                        ->label($isArabic ? 'رابط التطبيق' : 'App URL')
                        ->url()
                        ->placeholder($isArabic ? 'https://example.com' : 'https://example.com')
                        ->helperText($isArabic ? 'رابط التطبيق أو الخدمة' : 'App or service URL'),
                ])
                ->columns(2),
        ]);
    }
}

