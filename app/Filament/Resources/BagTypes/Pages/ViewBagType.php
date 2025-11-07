<?php

namespace App\Filament\Resources\BagTypes\Pages;

use App\Filament\Resources\BagTypes\BagTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBagType extends ViewRecord
{
    protected static string $resource = BagTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

