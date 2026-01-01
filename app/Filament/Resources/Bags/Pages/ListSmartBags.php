<?php

namespace App\Filament\Resources\Bags\Pages;

use App\Filament\Resources\Bags\SmartBagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSmartBags extends ListRecords
{
    protected static string $resource = SmartBagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

