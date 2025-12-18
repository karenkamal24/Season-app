<?php

namespace App\Filament\Resources\GeographicalGuides\Pages;

use App\Filament\Resources\GeographicalGuides\GeographicalGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeographicalGuides extends ListRecords
{
    protected static string $resource = GeographicalGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}



