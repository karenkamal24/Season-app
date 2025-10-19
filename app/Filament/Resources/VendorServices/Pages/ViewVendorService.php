<?php

namespace App\Filament\Resources\VendorServices\Pages;

use App\Filament\Resources\VendorServices\VendorServiceResource;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Select;

class ViewVendorService extends ViewRecord
{
    protected static string $resource = VendorServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            Action::make('change_status')
                ->label('Change Status')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('warning')
                ->requiresConfirmation()
                ->form([
                    Select::make('status')
                        ->label('Select new status')
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'disabled' => 'Disabled',
                        ])
                        ->default(fn ($record) => $record->status)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $this->record->update(['status' => $data['status']]);

                    $this->redirect(static::getUrl(['record' => $this->record]));
                })
                ->successNotificationTitle('Status updated successfully!'),

        ];
    }
}
