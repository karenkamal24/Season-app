<?php

namespace App\Filament\Resources\GeographicalCategories\Pages;

use App\Filament\Resources\GeographicalCategories\GeographicalCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeographicalCategory extends EditRecord
{
    protected static string $resource = GeographicalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

}



