<?php

namespace App\Filament\Resources\CategoryApps\Pages;

use App\Filament\Resources\CategoryApps\CategoryAppResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCategoryApp extends ViewRecord
{
    protected static string $resource = CategoryAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

