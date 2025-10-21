<?php

namespace App\Filament\Resources\EmergencyNumbers\Pages;

use App\Filament\Resources\EmergencyNumbers\EmergencyNumberResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEmergencyNumber extends EditRecord
{
    protected static string $resource = EmergencyNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
