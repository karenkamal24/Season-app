<?php

namespace App\Filament\Resources\BagTypes\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class BagTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                Section::make($isArabic ? 'معلومات نوع الحقيبة' : 'Bag Type Information')
                    ->description($isArabic ? 'أدخل تفاصيل نوع الحقيبة.' : 'Enter the bag type details.')
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

                                TextInput::make('default_max_weight')
                                    ->label($isArabic ? 'الوزن الأقصى الافتراضي' : 'Default Max Weight')
                                    ->numeric()
                                    ->step(0.01)
                                    ->suffix('kg')
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label($isArabic ? 'نشط' : 'Is Active')
                                    ->default(true)
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make($isArabic ? 'الأوصاف' : 'Descriptions')
                    ->description($isArabic ? 'أوصاف نوع الحقيبة باللغتين.' : 'Bag type descriptions in both languages.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Textarea::make('description_ar')
                                    ->label($isArabic ? 'الوصف بالعربي' : 'Arabic Description')
                                    ->rows(3)
                                    ->maxLength(1000),

                                Textarea::make('description_en')
                                    ->label($isArabic ? 'الوصف بالإنجليزي' : 'English Description')
                                    ->rows(3)
                                    ->maxLength(1000),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

