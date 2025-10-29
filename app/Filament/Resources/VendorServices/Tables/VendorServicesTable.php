<?php

namespace App\Filament\Resources\VendorServices\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\Select;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use App\Services\FirebaseService;
use Filament\Notifications\Notification;

class VendorServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Vendor')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-user'),

                TextColumn::make('serviceType.name_en')
                    ->label('Service Type')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-briefcase'),

                TextColumn::make('name')
                    ->label('Service Name')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-building-storefront'),

                TextColumn::make('contact_number')
                    ->label('Phone')
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->searchable(),

                TextColumn::make('address')
                    ->label('Address')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state)
                    ->icon('heroicon-o-map-pin')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('commercial_register')
                    ->label('Commercial File')
                    ->boolean()
                    ->tooltip('Has uploaded commercial register'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'disabled' => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        'disabled' => 'heroicon-o-pause-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->label('Status')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'disabled' => 'Disabled',
                    ]),
            ])

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info')
                        ->icon('heroicon-o-eye'),

                    EditAction::make()
                        ->color('primary')
                        ->icon('heroicon-o-pencil-square'),

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
                                ->required(),
                        ])
                        ->action(function ($record, array $data, FirebaseService $firebase): void {
                            $oldStatus = $record->status;
                            $newStatus = $data['status'];
                            
                            // Update the status
                            $record->update(['status' => $newStatus]);
                            
                            // Send notification to vendor if status changed
                            if ($oldStatus !== $newStatus && $record->user && $record->user->fcm_token) {
                                $statusMessages = [
                                    'approved' => [
                                        'title' => '✅ خدمتك تمت الموافقة عليها!',
                                        'body' => 'تم الموافقة على خدمة "' . $record->name . '" وهي الآن متاحة للعملاء',
                                        'icon' => '✅',
                                        'title_en' => '✅ Your Service Approved!',
                                        'body_en' => 'Your service "' . $record->name . '" has been approved and is now live',
                                    ],
                                    'rejected' => [
                                        'title' => '❌ تم رفض خدمتك',
                                        'body' => 'للأسف، تم رفض خدمة "' . $record->name . '". يرجى مراجعة البيانات وإعادة التقديم',
                                        'icon' => '❌',
                                        'title_en' => '❌ Service Rejected',
                                        'body_en' => 'Unfortunately, your service "' . $record->name . '" was rejected. Please review and resubmit',
                                    ],
                                    'pending' => [
                                        'title' => '⏳ خدمتك قيد المراجعة',
                                        'body' => 'خدمة "' . $record->name . '" الآن قيد المراجعة من قبل الإدارة',
                                        'icon' => '⏳',
                                        'title_en' => '⏳ Service Under Review',
                                        'body_en' => 'Your service "' . $record->name . '" is now under admin review',
                                    ],
                                    'disabled' => [
                                        'title' => '⏸️ تم تعطيل خدمتك',
                                        'body' => 'تم تعطيل خدمة "' . $record->name . '" مؤقتاً',
                                        'icon' => '⏸️',
                                        'title_en' => '⏸️ Service Disabled',
                                        'body_en' => 'Your service "' . $record->name . '" has been temporarily disabled',
                                    ],
                                ];
                                
                                $message = $statusMessages[$newStatus] ?? null;
                                
                                if ($message) {
                                    try {
                                        $firebase->sendToDevice(
                                            $record->user->fcm_token,
                                            $message['title'],
                                            $message['body'],
                                            [
                                                'type' => 'vendor_service_status_change',
                                                'service_id' => (string) $record->id,
                                                'service_name' => $record->name,
                                                'old_status' => $oldStatus,
                                                'new_status' => $newStatus,
                                                'icon' => $message['icon'],
                                            ]
                                        );
                                        
                                        Notification::make()
                                            ->success()
                                            ->title('Status updated & notification sent!')
                                            ->body("Vendor {$record->user->name} has been notified")
                                            ->send();
                                    } catch (\Exception $e) {
                                        Notification::make()
                                            ->warning()
                                            ->title('Status updated')
                                            ->body('Status changed but notification failed: ' . $e->getMessage())
                                            ->send();
                                    }
                                }
                            }
                        })
                        ->successNotificationTitle('Status updated successfully!'),

                    DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ]),
            ]);



    }
}
