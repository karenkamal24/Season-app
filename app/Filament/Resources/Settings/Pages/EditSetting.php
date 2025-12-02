<?php

namespace App\Filament\Resources\Settings\Pages;

use App\Filament\Resources\Settings\SettingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $settingOptions = \App\Filament\Resources\Settings\Schemas\SettingForm::getSettingOptions();

        // Find the key that matches the current name.en
        if (isset($data['name']['en'])) {
            foreach ($settingOptions as $key => $values) {
                if ($values['en'] === $data['name']['en']) {
                    $data['name_key'] = $key;
                    break;
                }
            }
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
