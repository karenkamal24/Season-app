<?php

namespace App\Filament\Resources\EmergencyNumbers\Pages;

use App\Filament\Resources\EmergencyNumbers\EmergencyNumberResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmergencyNumber extends ViewRecord
{
    protected static string $resource = EmergencyNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
