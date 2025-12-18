<?php

namespace App\Filament\Resources\GeographicalGuides\Pages;

use App\Filament\Resources\GeographicalGuides\GeographicalGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGeographicalGuide extends ViewRecord
{
    protected static string $resource = GeographicalGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}



