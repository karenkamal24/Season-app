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
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BagTypeResource extends Resource
{
    protected static ?string $model = BagType::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Bag Types';

    protected static ?string $recordTitleAttribute = 'name_ar';

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

