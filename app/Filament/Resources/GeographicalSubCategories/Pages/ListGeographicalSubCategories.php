<?php

namespace App\Filament\Resources\GeographicalSubCategories\Pages;

use App\Filament\Resources\GeographicalSubCategories\GeographicalSubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeographicalSubCategories extends ListRecords
{
    protected static string $resource = GeographicalSubCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

