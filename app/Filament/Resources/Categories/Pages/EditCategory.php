<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If remove_icon is checked, set icon to null
        if (!empty($data['remove_icon'])) {
            // Delete old file if exists
            if ($this->record->icon && !str_starts_with($this->record->icon, 'http')) {
                $filePath = $this->record->icon;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
            $data['icon'] = null;
        }

        // Remove remove_icon from data as it's not a database field
        unset($data['remove_icon']);

        return $data;
    }
}

