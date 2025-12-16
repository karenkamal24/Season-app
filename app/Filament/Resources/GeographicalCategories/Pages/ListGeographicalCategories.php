<?php

namespace App\Filament\Resources\GeographicalCategories\Pages;

use App\Filament\Resources\GeographicalCategories\GeographicalCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeographicalCategories extends ListRecords
{
    protected static string $resource = GeographicalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

