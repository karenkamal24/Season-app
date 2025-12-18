<?php

namespace App\Filament\Resources\GeographicalGuides;

use App\Filament\Resources\GeographicalGuides\Pages\CreateGeographicalGuide;
use App\Filament\Resources\GeographicalGuides\Pages\EditGeographicalGuide;
use App\Filament\Resources\GeographicalGuides\Pages\ListGeographicalGuides;
use App\Filament\Resources\GeographicalGuides\Pages\ViewGeographicalGuide;
use App\Filament\Resources\GeographicalGuides\Schemas\GeographicalGuideForm;
use App\Filament\Resources\GeographicalGuides\Schemas\GeographicalGuideInfolist;
use App\Filament\Resources\GeographicalGuides\Tables\GeographicalGuidesTable;
use App\Models\GeographicalGuide;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GeographicalGuideResource extends Resource
{
    protected static ?string $model = GeographicalGuide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $recordTitleAttribute = 'service_name';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'الدليل الجغرافي' : 'Geographical Guide';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الدليل الجغرافي' : 'Geographical Guides';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'دليل جغرافي' : 'Geographical Guide';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'الدليل الجغرافي' : 'Geographical Guides';
    }

    public static function form(Schema $schema): Schema
    {
        return GeographicalGuideForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GeographicalGuideInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GeographicalGuidesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGeographicalGuides::route('/'),
            'create' => CreateGeographicalGuide::route('/create'),
            'view' => ViewGeographicalGuide::route('/{record}'),
            'edit' => EditGeographicalGuide::route('/{record}/edit'),
        ];
    }
}



