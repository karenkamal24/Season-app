<?php

namespace App\Filament\Resources\ServiceTypes\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema
            ->components([
                TextInput::make('name_ar')
                    ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name')
                    ->required(),
                
                TextInput::make('name_en')
                    ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name')
                    ->required(),
                
                Toggle::make('is_active')
                    ->label($isArabic ? 'نشط' : 'Is Active')
                    ->required(),
            ]);
    }
}
