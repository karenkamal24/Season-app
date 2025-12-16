<?php

namespace App\Filament\Resources\GeographicalSubCategories\Pages;

use App\Filament\Resources\GeographicalSubCategories\GeographicalSubCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeographicalSubCategory extends EditRecord
{
    protected static string $resource = GeographicalSubCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

