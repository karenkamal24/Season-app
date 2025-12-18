<?php

namespace App\Filament\Resources\GeographicalSubCategories\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class GeographicalSubCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات التصنيف الفرعي' : 'Sub Category Information')
                ->description($isArabic ? 'المعلومات الأساسية عن التصنيف الفرعي' : 'Basic information about the geographical sub category')
                ->schema([
                    Select::make('geographical_category_id')
                        ->label($isArabic ? 'التصنيف الرئيسي' : 'Main Category')
                        ->relationship('category', $isArabic ? 'name_ar' : 'name_en')
                        ->searchable()
                        ->preload()
                        ->required(),

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
        ]);
    }
}



