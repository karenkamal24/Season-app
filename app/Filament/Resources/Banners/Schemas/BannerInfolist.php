<?php

namespace App\Filament\Resources\Banners\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Schemas\Components\Section;

class BannerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema
            ->columns(1) // تعيين عمود واحد للعرض الكامل
            ->components([
                Section::make($isArabic ? 'صورة البانر' : 'Banner Image')
                    ->description($isArabic ? 'صورة البانر' : 'Banner image')
                    ->schema([
                        ImageEntry::make('image')
                            ->label($isArabic ? 'صورة البانر' : 'Banner Image')
                            ->getStateUsing(function ($record) {
                                if (!$record->image) {
                                    return null;
                                }

                                if (str_starts_with($record->image, 'http')) {
                                    return $record->image;
                                }

                                return asset('storage/' . $record->image);
                            })
                            ->hiddenLabel()
                            ->extraAttributes(['style' => 'width: 100%; max-width: 100%; height: auto;'])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make($isArabic ? 'معلومات البانر' : 'Banner Information')
                    ->description($isArabic ? 'تفاصيل البانر' : 'Banner details')
                    ->inlineLabel()
                    ->schema([
                        TextEntry::make('language')
                            ->label($isArabic ? 'اللغة' : 'Language')
                            ->formatStateUsing(fn($record) => $record->language === 'ar'
                                ? ($isArabic ? 'العربية' : 'Arabic')
                                : ($isArabic ? 'الإنجليزية' : 'English'))
                            ->badge()
                            ->color('success'),

                        TextEntry::make('route')
                            ->label($isArabic ? 'مسار التطبيق' : 'App Route')
                            ->placeholder('-')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('is_active')
                            ->label($isArabic ? 'نشط' : 'Active')
                            ->badge()
                            ->formatStateUsing(fn($state) => $state
                                ? ($isArabic ? 'نشط' : 'Active')
                                : ($isArabic ? 'غير نشط' : 'Inactive'))
                            ->color(fn($state) => $state ? 'success' : 'danger'),

                        TextEntry::make('created_at')
                            ->label($isArabic ? 'تاريخ الإنشاء' : 'Created At')
                            ->dateTime(),

                        TextEntry::make('updated_at')
                            ->label($isArabic ? 'تاريخ التحديث' : 'Updated At')
                            ->dateTime(),
                    ])
                    ->columnSpanFull(), // القسم يأخذ العرض الكامل
            ]);
    }
}
