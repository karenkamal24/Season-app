<?php

namespace App\Filament\Resources\Bags\Pages;

use App\Filament\Resources\Bags\SmartBagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSmartBag extends EditRecord
{
    protected static string $resource = SmartBagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

