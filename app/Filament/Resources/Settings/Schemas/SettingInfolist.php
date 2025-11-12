<?php

namespace App\Filament\Resources\Settings\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                TextEntry::make('name.en')
                    ->label($isArabic ? 'الاسم (إنجليزي)' : 'Name (English)')
                    ->placeholder('-'),

                TextEntry::make('name.ar')
                    ->label($isArabic ? 'الاسم (عربي)' : 'Name (Arabic)')
                    ->placeholder('-'),

                TextEntry::make('value')
                    ->label($isArabic ? 'القيمة' : 'Value')
                    ->numeric(),

                TextEntry::make('max')
                    ->label($isArabic ? 'الحد الأقصى' : 'Max')
                    ->numeric(),

                TextEntry::make('created_at')
                    ->label($isArabic ? 'تاريخ الإنشاء' : 'Created')
                    ->dateTime()
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label($isArabic ? 'تاريخ التحديث' : 'Updated')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
