<?php

namespace App\Filament\Resources\BagTypes;

use App\Filament\Resources\BagTypes\Pages\CreateBagType;
use App\Filament\Resources\BagTypes\Pages\EditBagType;
use App\Filament\Resources\BagTypes\Pages\ListBagTypes;
use App\Filament\Resources\BagTypes\Pages\ViewBagType;
use App\Filament\Resources\BagTypes\Schemas\BagTypeForm;
use App\Filament\Resources\BagTypes\Schemas\BagTypeInfolist;
use App\Filament\Resources\BagTypes\Tables\BagTypesTable;
use App\Models\BagType;
use App\Helpers\LanguageHelper;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BagTypeResource extends Resource
{
    protected static ?string $model = BagType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $recordTitleAttribute = 'name_ar';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'حقيبة' : 'Bag';
    }

    public static function getNavigationLabel(): string
    {
        return LanguageHelper::translate('bag_types', [
            'ar' => 'أنواع الحقائب',
            'en' => 'Bag Types'
        ]);
    }

    public static function getModelLabel(): string
    {
        return LanguageHelper::translate('bag_type', [
            'ar' => 'نوع حقيبة',
            'en' => 'Bag Type'
        ]);
    }

    public static function getPluralModelLabel(): string
    {
        return LanguageHelper::translate('bag_types', [
            'ar' => 'أنواع الحقائب',
            'en' => 'Bag Types'
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return BagTypeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BagTypeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BagTypesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBagTypes::route('/'),
            'create' => CreateBagType::route('/create'),
            'view' => ViewBagType::route('/{record}'),
            'edit' => EditBagType::route('/{record}/edit'),
        ];
    }
}

