<?php

namespace App\Filament\Resources\CategoryApps;

use App\Filament\Resources\CategoryApps\Pages\CreateCategoryApp;
use App\Filament\Resources\CategoryApps\Pages\EditCategoryApp;
use App\Filament\Resources\CategoryApps\Pages\ListCategoryApps;
use App\Filament\Resources\CategoryApps\Pages\ViewCategoryApp;
use App\Filament\Resources\CategoryApps\Schemas\CategoryAppForm;
use App\Filament\Resources\CategoryApps\Schemas\CategoryAppInfolist;
use App\Filament\Resources\CategoryApps\Tables\CategoryAppsTable;
use App\Models\CategoryApp;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CategoryAppResource extends Resource
{
    protected static ?string $model = CategoryApp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDevicePhoneMobile;

    protected static ?string $recordTitleAttribute = 'name_ar';

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() === 'ar' ? 'الدليل الرقمي' : 'Digital Directory';
    }

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تطبيقات ' : ' Apps';
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تطبيق' : 'App';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'ar' ? 'تطبيقات ' : ' Apps';
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryAppForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CategoryAppInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoryAppsTable::configure($table);
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
            'index' => ListCategoryApps::route('/'),
            'create' => CreateCategoryApp::route('/create'),
            'view' => ViewCategoryApp::route('/{record}'),
            'edit' => EditCategoryApp::route('/{record}/edit'),
        ];
    }
}

