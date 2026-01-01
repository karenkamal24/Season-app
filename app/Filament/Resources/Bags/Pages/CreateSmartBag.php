<?php

namespace App\Filament\Resources\Bags\Pages;

use App\Filament\Resources\Bags\SmartBagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSmartBag extends CreateRecord
{
    protected static string $resource = SmartBagResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

