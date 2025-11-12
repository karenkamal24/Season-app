<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages\CreateSetting;
use App\Filament\Resources\Settings\Pages\EditSetting;
use App\Filament\Resources\Settings\Pages\ListSettings;
use App\Filament\Resources\Settings\Pages\ViewSetting;
use App\Filament\Resources\Settings\Schemas\SettingForm;
use App\Filament\Resources\Settings\Schemas\SettingInfolist;
use App\Filament\Resources\Settings\Tables\SettingsTable;
use App\Models\Setting;
use BackedEnum;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;
    
    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'الإعدادات' : 'Settings';
    }
    
    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'النقاط' : 'Points';
    }
    
    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'نقطة' : 'Coin';
    }
    
    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'النقاط' : 'Points';
    }
    public static function form(Schema $schema): Schema
    {
        return SettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }


    public static function getRecordTitle(?Model $record): string|Htmlable|null
    {
        if (! $record) {
            return null;
        }

        return $record->localized_name;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'view' => ViewSetting::route('/{record}'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
