<?php

namespace App\Filament\Resources\ItemCategories\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class ItemCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema
            ->components([
                Section::make($isArabic ? 'معلومات التصنيف' : 'Category Information')
                    ->description($isArabic ? 'أدخل تفاصيل التصنيف.' : 'Enter the category details.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name_ar')
                                    ->label($isArabic ? 'الاسم بالعربي' : 'Arabic Name')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('name_en')
                                    ->label($isArabic ? 'الاسم بالإنجليزي' : 'English Name')
                                    ->required()
                                    ->maxLength(255),

                                FileUpload::make('icon')
                                    ->label($isArabic ? 'أيقونة التصنيف' : 'Category Icon')
                                    ->disk('public')
                                    ->directory('item-categories')
                                    ->visibility('public')
                                    ->image()
                                    ->imageEditor()
                                    ->imagePreviewHeight('150')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/gif'])
                                    ->downloadable()
                                    ->openable()
                                    ->required(fn (string $operation): bool => $operation === 'create')
                                    ->columnSpanFull()
                                    ->deletable(true)
                                    ->preserveFilenames(false)
                                    ->afterStateHydrated(function (FileUpload $component, $state) {
                                        // خلي الـ state فاضي في Edit عشان ميحاولش يحمل الصورة
                                        if (request()->routeIs('filament.*.resources.*.edit')) {
                                            $component->state(null);
                                        }
                                    }),

                                ColorPicker::make('icon_color')
                                    ->label($isArabic ? 'لون الأيقونة' : 'Icon Color')
                                    ->default('#000000'),

                                TextInput::make('sort_order')
                                    ->label($isArabic ? 'ترتيب العرض' : 'Sort Order')
                                    ->numeric()
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label($isArabic ? 'نشط' : 'Is Active')
                                    ->default(true)
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

