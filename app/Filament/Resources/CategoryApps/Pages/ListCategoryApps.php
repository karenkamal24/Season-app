<?php

namespace App\Filament\Resources\CategoryApps\Pages;

use App\Filament\Resources\CategoryApps\CategoryAppResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCategoryApps extends ListRecords
{
    protected static string $resource = CategoryAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

