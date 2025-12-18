<?php

namespace App\Filament\Resources\GeographicalGuides\Pages;

use App\Filament\Resources\GeographicalGuides\GeographicalGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeographicalGuide extends EditRecord
{
    protected static string $resource = GeographicalGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}



