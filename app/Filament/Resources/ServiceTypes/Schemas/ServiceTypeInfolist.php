<?php

namespace App\Filament\Resources\ServiceTypes\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServiceTypeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                TextEntry::make('name_ar')
                    ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name'),
                
                TextEntry::make('name_en')
                    ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name'),
                
                IconEntry::make('is_active')
                    ->boolean()
                    ->label($isArabic ? 'نشط' : 'Is Active'),
                
                TextEntry::make('created_at')
                    ->label($isArabic ? 'تاريخ الإنشاء' : 'Created At')
                    ->dateTime()
                    ->placeholder('-'),
                
                TextEntry::make('updated_at')
                    ->label($isArabic ? 'تاريخ التحديث' : 'Updated At')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
