<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات التصنيف' : 'Category Information')
                ->description($isArabic ? 'المعلومات الأساسية عن التصنيف' : 'Basic information about the category')
                ->schema([
                    Select::make('countries')
                        ->label($isArabic ? 'الدول' : 'Countries')
                        ->relationship('countries', $isArabic ? 'name_ar' : 'name_en')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->getOptionLabelFromRecordUsing(fn($record) => $isArabic ? $record->name_ar : $record->name_en)
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

            Section::make($isArabic ? 'الأيقونة' : 'Icon')
                ->icon('heroicon-o-photo')
                ->schema([
                    FileUpload::make('icon')
                        ->label($isArabic ? 'أيقونة التصنيف' : 'Category Icon')
                        ->directory('categories/icons')
                        ->image()
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->imageEditor()
                        ->helperText($isArabic ? 'صورة أيقونة التصنيف' : 'Category icon image')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}

