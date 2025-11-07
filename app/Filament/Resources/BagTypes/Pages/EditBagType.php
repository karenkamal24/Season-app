<?php

namespace App\Filament\Resources\BagTypes\Pages;

use App\Filament\Resources\BagTypes\BagTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBagType extends EditRecord
{
    protected static string $resource = BagTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

