<?php

namespace App\Filament\Resources\EmergencyNumbers;

use App\Filament\Resources\EmergencyNumbers\Pages\CreateEmergencyNumber;
use App\Filament\Resources\EmergencyNumbers\Pages\EditEmergencyNumber;
use App\Filament\Resources\EmergencyNumbers\Pages\ListEmergencyNumbers;
use App\Filament\Resources\EmergencyNumbers\Pages\ViewEmergencyNumber;
use App\Filament\Resources\EmergencyNumbers\Schemas\EmergencyNumberForm;
use App\Filament\Resources\EmergencyNumbers\Schemas\EmergencyNumberInfolist;
use App\Filament\Resources\EmergencyNumbers\Tables\EmergencyNumbersTable;
use App\Models\EmergencyNumber;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmergencyNumberResource extends Resource
{
    protected static ?string $model = EmergencyNumber::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice;

protected static string|\UnitEnum|null $navigationGroup = 'EmergencyNumber';
    protected static ?string $recordTitleAttribute = 'country_id';

    public static function form(Schema $schema): Schema
    {
        return EmergencyNumberForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmergencyNumberInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmergencyNumbersTable::configure($table);
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
            'index' => ListEmergencyNumbers::route('/'),
            'create' => CreateEmergencyNumber::route('/create'),
            'view' => ViewEmergencyNumber::route('/{record}'),
            'edit' => EditEmergencyNumber::route('/{record}/edit'),
        ];
    }
}
