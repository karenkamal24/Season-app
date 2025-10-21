<?php

namespace App\Filament\Resources\VendorServices;

use App\Filament\Resources\VendorServices\Pages\CreateVendorService;
use App\Filament\Resources\VendorServices\Pages\EditVendorService;
use App\Filament\Resources\VendorServices\Pages\ListVendorServices;
use App\Filament\Resources\VendorServices\Pages\ViewVendorService;
use App\Filament\Resources\VendorServices\Schemas\VendorServiceForm;
use App\Filament\Resources\VendorServices\Schemas\VendorServiceInfolist;
use App\Filament\Resources\VendorServices\Tables\VendorServicesTable;
use App\Models\VendorService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VendorServiceResource extends Resource
{
    protected static ?string $model = VendorService::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

protected static string|\UnitEnum|null $navigationGroup = 'Customers';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VendorServiceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VendorServiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VendorServicesTable::configure($table);
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
            'index' => ListVendorServices::route('/'),
            'create' => CreateVendorService::route('/create'),
            'view' => ViewVendorService::route('/{record}'),
            'edit' => EditVendorService::route('/{record}/edit'),
        ];
    }
}
