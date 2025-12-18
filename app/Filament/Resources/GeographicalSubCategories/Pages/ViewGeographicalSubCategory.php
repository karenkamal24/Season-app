<?php

namespace App\Filament\Resources\GeographicalSubCategories\Pages;

use App\Filament\Resources\GeographicalSubCategories\GeographicalSubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGeographicalSubCategory extends ViewRecord
{
    protected static string $resource = GeographicalSubCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}



