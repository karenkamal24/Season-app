<?php

namespace App\Filament\Resources\GeographicalCategories;

use App\Filament\Resources\GeographicalCategories\Pages\CreateGeographicalCategory;
use App\Filament\Resources\GeographicalCategories\Pages\EditGeographicalCategory;
use App\Filament\Resources\GeographicalCategories\Pages\ListGeographicalCategories;
use App\Filament\Resources\GeographicalCategories\Pages\ViewGeographicalCategory;
use App\Filament\Resources\GeographicalCategories\Schemas\GeographicalCategoryForm;
use App\Filament\Resources\GeographicalCategories\Schemas\GeographicalCategoryInfolist;
use App\Filament\Resources\GeographicalCategories\Tables\GeographicalCategoriesTable;
use App\Models\GeographicalCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GeographicalCategoryResource extends Resource
{
    protected static ?string $model = GeographicalCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static ?string $recordTitleAttribute = 'name_ar';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'الدليل الجغرافي' : 'Geographical Guide';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'التصنيفات الجغرافية' : 'Geographical Categories';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تصنيف جغرافي' : 'Geographical Category';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'التصنيفات الجغرافية' : 'Geographical Categories';
    }

    public static function form(Schema $schema): Schema
    {
        return GeographicalCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GeographicalCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GeographicalCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGeographicalCategories::route('/'),
            'create' => CreateGeographicalCategory::route('/create'),
            'view' => ViewGeographicalCategory::route('/{record}'),
            'edit' => EditGeographicalCategory::route('/{record}/edit'),
        ];
    }
}



