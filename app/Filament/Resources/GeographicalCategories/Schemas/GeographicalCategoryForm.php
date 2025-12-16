<?php

namespace App\Filament\Resources\GeographicalCategories\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class GeographicalCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات التصنيف' : 'Category Information')
                ->description($isArabic ? 'المعلومات الأساسية عن التصنيف الجغرافي' : 'Basic information about the geographical category')
                ->schema([
                    TextInput::make('name_ar')
                        ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('name_en')
                        ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name')
                        ->required()
                        ->maxLength(255),

                    Toggle::make('is_active')
                        ->label($isArabic ? 'نشط' : 'Is Active')
                        ->default(true)
                        ->required(),
                ])
                ->columns(2),

            Section::make($isArabic ? 'الأيقونة' : 'Icon')
                ->icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make('icon')
                        ->label($isArabic ? 'أيقونة التصنيف' : 'Category Icon')
                        ->disk('public')
                        ->directory('geographical_categories/icons')
                        ->image()
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->imageEditor()
                        ->helperText($isArabic ? 'صورة أيقونة التصنيف الجغرافي' : 'Geographical category icon image')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}

