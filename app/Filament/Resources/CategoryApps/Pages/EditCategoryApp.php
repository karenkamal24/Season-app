<?php

namespace App\Filament\Resources\CategoryApps\Pages;

use App\Filament\Resources\CategoryApps\CategoryAppResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoryApp extends EditRecord
{
    protected static string $resource = CategoryAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

