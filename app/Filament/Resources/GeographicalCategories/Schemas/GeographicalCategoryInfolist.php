<?php

namespace App\Filament\Resources\GeographicalCategories\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class GeographicalCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات التصنيف' : 'Category Information')
                ->description($isArabic ? 'تفاصيل التصنيف الجغرافي' : 'Geographical category details')
                ->inlineLabel()
                ->components([
                    TextEntry::make('name_ar')
                        ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name')
                        ->placeholder('-'),

                    TextEntry::make('name_en')
                        ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name')
                        ->placeholder('-'),

                    TextEntry::make('subCategories.count')
                        ->label($isArabic ? 'عدد التصنيفات الفرعية' : 'Sub Categories Count')
                        ->placeholder('0'),

                    IconEntry::make('is_active')
                        ->boolean()
                        ->label($isArabic ? 'نشط' : 'Is Active'),
                ]),

            Section::make($isArabic ? 'الأيقونة' : 'Icon')
                ->icon('heroicon-o-photo')
                ->inlineLabel()
                ->components([
                    ImageEntry::make('icon')
                        ->label($isArabic ? 'أيقونة التصنيف' : 'Category Icon')
                        ->hiddenLabel()
                        ->width('100px')
                        ->height('100px')
                        ->getStateUsing(fn($record) => $record->icon_url),
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

