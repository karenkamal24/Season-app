<?php

namespace App\Filament\Resources\Banners\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema
            ->columns(1)
            ->components([
                Section::make($isArabic ? 'معلومات البانر' : 'Banner Information')
                    ->schema([
                        Select::make('country_id')
                            ->label($isArabic ? 'البلد' : 'Country')
                            ->relationship('country', $isArabic ? 'name_ar' : 'name_en',
                                fn ($query) => $query->whereIn('code', ['KSA', 'UAE', 'EGY']))
                            ->searchable(['name_en', 'name_ar', 'code'])
                            ->preload()
                            ->required(),

                        Select::make('language')
                            ->label($isArabic ? 'اللغة' : 'Language')
                            ->options([
                                'en' => $isArabic ? 'الإنجليزية' : 'English',
                                'ar' => $isArabic ? 'العربية' : 'Arabic',
                            ])
                            ->required(),

                        FileUpload::make('image')
                            ->label($isArabic ? 'صورة البانر' : 'Banner Image')
                            ->disk('public')                // important
                            ->directory('banners')          // saved inside storage/app/public/banners
                            ->visibility('public')          // allow public access
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label($isArabic ? 'نشط' : 'Active')
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
