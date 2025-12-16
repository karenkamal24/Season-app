<?php

namespace App\Filament\Resources\GeographicalCategories\Pages;

use App\Filament\Resources\GeographicalCategories\GeographicalCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGeographicalCategory extends ViewRecord
{
    protected static string $resource = GeographicalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

