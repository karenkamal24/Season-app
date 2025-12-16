<?php

namespace App\Filament\Resources\GeographicalSubCategories;

use App\Filament\Resources\GeographicalSubCategories\Pages\CreateGeographicalSubCategory;
use App\Filament\Resources\GeographicalSubCategories\Pages\EditGeographicalSubCategory;
use App\Filament\Resources\GeographicalSubCategories\Pages\ListGeographicalSubCategories;
use App\Filament\Resources\GeographicalSubCategories\Pages\ViewGeographicalSubCategory;
use App\Filament\Resources\GeographicalSubCategories\Schemas\GeographicalSubCategoryForm;
use App\Filament\Resources\GeographicalSubCategories\Schemas\GeographicalSubCategoryInfolist;
use App\Filament\Resources\GeographicalSubCategories\Tables\GeographicalSubCategoriesTable;
use App\Models\GeographicalSubCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GeographicalSubCategoryResource extends Resource
{
    protected static ?string $model = GeographicalSubCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?string $recordTitleAttribute = 'name_ar';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'الدليل الجغرافي' : 'Geographical Guide';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'التصنيفات الفرعية' : 'Geographical Sub Categories';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تصنيف فرعي' : 'Geographical Sub Category';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'التصنيفات الفرعية' : 'Geographical Sub Categories';
    }

    public static function form(Schema $schema): Schema
    {
        return GeographicalSubCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GeographicalSubCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GeographicalSubCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGeographicalSubCategories::route('/'),
            'create' => CreateGeographicalSubCategory::route('/create'),
            'view' => ViewGeographicalSubCategory::route('/{record}'),
            'edit' => EditGeographicalSubCategory::route('/{record}/edit'),
        ];
    }
}

