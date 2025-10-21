<?php

namespace App\Filament\Resources\EmergencyNumbers\Pages;

use App\Filament\Resources\EmergencyNumbers\EmergencyNumberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmergencyNumbers extends ListRecords
{
    protected static string $resource = EmergencyNumberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
