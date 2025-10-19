<?php

namespace App\Filament\Resources\VendorServices\Pages;

use App\Filament\Resources\VendorServices\VendorServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVendorServices extends ListRecords
{
    protected static string $resource = VendorServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
