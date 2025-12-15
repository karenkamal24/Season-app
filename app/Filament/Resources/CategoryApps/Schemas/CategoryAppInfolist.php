<?php

namespace App\Filament\Resources\CategoryApps\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CategoryAppInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات التطبيق' : 'App Information')
                ->description($isArabic ? 'تفاصيل التطبيق الرئيسية' : 'Main app details')
                ->inlineLabel()
                ->components([
                    TextEntry::make('category.name_ar')
                        ->label($isArabic ? 'التصنيف (عربي)' : 'Category (Arabic)')
                        ->placeholder('-'),

                    TextEntry::make('category.name_en')
                        ->label($isArabic ? 'التصنيف (إنجليزي)' : 'Category (English)')
                        ->placeholder('-'),

                    TextEntry::make('country.name_ar')
                        ->label($isArabic ? 'الدولة (عربي)' : 'Country (Arabic)')
                        ->placeholder('-'),

                    TextEntry::make('country.name_en')
                        ->label($isArabic ? 'الدولة (إنجليزي)' : 'Country (English)')
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

            Section::make($isArabic ? 'الصورة والرابط' : 'Icon & URL')
                ->icon('heroicon-o-photo')
                ->inlineLabel()
                ->components([
                    ImageEntry::make('icon')
                        ->label($isArabic ? 'أيقونة التطبيق' : 'App Icon')
                        ->hiddenLabel()
                        ->width('100px')
                        ->height('100px')
                        ->getStateUsing(fn($record) => $record->icon_url),

                    TextEntry::make('url')
                        ->label($isArabic ? 'رابط التطبيق' : 'App URL')
                        ->url(fn($record) => $record->url)
                        ->openUrlInNewTab()
                        ->placeholder('-'),
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

