<?php

namespace App\Filament\Resources\Bags\Pages;

use App\Filament\Resources\Bags\SmartBagResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSmartBag extends ViewRecord
{
    protected static string $resource = SmartBagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

