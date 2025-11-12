<?php

namespace App\Filament\Resources\EmergencyNumbers\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class EmergencyNumberForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();
        
        return $schema->components([
            Section::make($isArabic ? 'معلومات الطوارئ' : 'Emergency Information')
                ->description($isArabic ? 'أدخل أرقام الطوارئ لكل دولة.' : 'Enter the emergency numbers for each country.')
                ->columnSpanFull()
                ->schema([
                    Grid::make(1)->schema([
                        Select::make('country_id')
                            ->label($isArabic ? 'الدولة' : 'Country')
                            ->relationship('country', 'name_ar')
                            ->searchable()
                            ->required()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name_ar} ({$record->code})")
                            ->columnSpanFull(),

                        Fieldset::make($isArabic ? 'أرقام الطوارئ' : 'Emergency Numbers')->schema([
                            TextInput::make('fire')
                                ->label($isArabic ? 'رقم الإطفاء' : 'Fire Number')
                                ->prefixIcon('heroicon-o-fire')
                                ->tel()
                                ->required(),

                            TextInput::make('police')
                                ->label($isArabic ? 'رقم الشرطة' : 'Police Number')
                                ->prefixIcon('heroicon-o-shield-check')
                                ->tel()
                                ->required(),

                            TextInput::make('ambulance')
                                ->label($isArabic ? 'رقم الإسعاف' : 'Ambulance Number')
                                ->prefixIcon('heroicon-o-truck')
                                ->tel()
                                ->required(),

                            TextInput::make('embassy')
                                ->label($isArabic ? 'رقم السفارة' : 'Embassy Number')
                                ->prefixIcon('heroicon-o-building-office')
                                ->tel()
                                ->required(),
                        ])->columns(2),
                    ]),
                ]),
        ]);
    }
}
