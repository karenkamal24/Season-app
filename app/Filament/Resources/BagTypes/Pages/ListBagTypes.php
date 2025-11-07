<?php

namespace App\Filament\Resources\BagTypes\Pages;

use App\Filament\Resources\BagTypes\BagTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBagTypes extends ListRecords
{
    protected static string $resource = BagTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

