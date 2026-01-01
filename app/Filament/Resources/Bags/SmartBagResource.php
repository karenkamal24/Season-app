<?php

namespace App\Filament\Resources\Bags;

use App\Filament\Resources\Bags\Pages\CreateSmartBag;
use App\Filament\Resources\Bags\Pages\EditSmartBag;
use App\Filament\Resources\Bags\Pages\ListSmartBags;
use App\Filament\Resources\Bags\Pages\ViewSmartBag;
use App\Filament\Resources\Bags\Schemas\SmartBagForm;
use App\Filament\Resources\Bags\Schemas\SmartBagInfolist;
use App\Filament\Resources\Bags\Tables\SmartBagsTable;
use App\Models\Bag;
use App\Helpers\LanguageHelper;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SmartBagResource extends Resource
{
    protected static ?string $model = Bag::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'الحقائب الذكية' : 'Smart Bags';
    }

    public static function getNavigationLabel(): string
    {
        return LanguageHelper::translate('smart_bags', [
            'ar' => 'الحقائب الذكية',
            'en' => 'Smart Bags'
        ]);
    }

    public static function getModelLabel(): string
    {
        return LanguageHelper::translate('smart_bag', [
            'ar' => 'حقيبة ذكية',
            'en' => 'Smart Bag'
        ]);
    }

    public static function getPluralModelLabel(): string
    {
        return LanguageHelper::translate('smart_bags', [
            'ar' => 'الحقائب الذكية',
            'en' => 'Smart Bags'
        ]);
    }

    public static function schema(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->form(SmartBagForm::getForm());
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->infolist(SmartBagInfolist::getInfolist());
    }

    public static function table(Table $table): Table
    {
        return SmartBagsTable::getTable($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmartBags::route('/'),
            'create' => CreateSmartBag::route('/create'),
            'edit' => EditSmartBag::route('/{record}/edit'),
            'view' => ViewSmartBag::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

