<?php

namespace App\Filament\Resources\GeographicalSubCategories\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class GeographicalSubCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات التصنيف الفرعي' : 'Sub Category Information')
                ->description($isArabic ? 'تفاصيل التصنيف الفرعي' : 'Geographical sub category details')
                ->inlineLabel()
                ->components([
                    TextEntry::make('category.name_ar')
                        ->label($isArabic ? 'التصنيف الرئيسي (عربي)' : 'Main Category (Arabic)')
                        ->placeholder('-'),

                    TextEntry::make('category.name_en')
                        ->label($isArabic ? 'التصنيف الرئيسي (إنجليزي)' : 'Main Category (English)')
                        ->placeholder('-'),

                    TextEntry::make('name_ar')
                        ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name')
                        ->placeholder('-'),

                    TextEntry::make('name_en')
                        ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name')
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



