<?php

namespace App\Filament\Resources\VendorServices\Tables;

use App\Helpers\LanguageHelper;
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
        $isArabic = LanguageHelper::isArabic();

        return $table
            ->modifyQueryUsing(function ($query) {
                $query->with(['user', 'serviceType']);
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label($isArabic ? 'البائع' : 'Vendor')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->formatStateUsing(fn ($record) => $record->user?->name ?? ($isArabic ? 'غير محدد' : 'N/A'))
                    ->placeholder($isArabic ? 'غير محدد' : 'N/A'),

                TextColumn::make('serviceType.name_en')
                    ->label($isArabic ? 'نوع الخدمة' : 'Service Type')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-briefcase')
                    ->formatStateUsing(fn ($record) => $record->serviceType?->name_en ?? ($isArabic ? 'غير محدد' : 'N/A'))
                    ->placeholder($isArabic ? 'غير محدد' : 'N/A'),

                TextColumn::make('name')
                    ->label($isArabic ? 'اسم الخدمة' : 'Service Name')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-building-storefront'),

                TextColumn::make('contact_number')
                    ->label($isArabic ? 'الهاتف' : 'Phone')
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->searchable(),

                TextColumn::make('address')
                    ->label($isArabic ? 'العنوان' : 'Address')
                    ->limit(30)
                    ->tooltip(fn ($state) => $state)
                    ->icon('heroicon-o-map-pin')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('commercial_register')
                    ->label($isArabic ? 'السجل التجاري' : 'Commercial File')
                    ->boolean()
                    ->tooltip($isArabic ? 'تم رفع السجل التجاري' : 'Has uploaded commercial register'),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
                        'approved' => $isArabic ? 'موافق عليها' : 'Approved',
                        'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                        'disabled' => $isArabic ? 'معطلة' : 'Disabled',
                        default => $state,
                    })
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
                    ->label($isArabic ? 'الحالة' : 'Status')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label($isArabic ? 'تاريخ الإنشاء' : 'Created At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label($isArabic ? 'تاريخ التحديث' : 'Updated At')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label($isArabic ? 'الحالة' : 'Status')
                    ->options([
                        'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
                        'approved' => $isArabic ? 'موافق عليها' : 'Approved',
                        'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                        'disabled' => $isArabic ? 'معطلة' : 'Disabled',
                    ]),
            ])

            ->recordActions([
                ViewAction::make()
                    ->color('info')
                    ->icon('heroicon-o-eye'),

                EditAction::make()
                    ->color('primary')
                    ->icon('heroicon-o-pencil-square'),

                Action::make('change_status')
                        ->label($isArabic ? 'تغيير الحالة' : 'Change Status')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->form([
                            Select::make('status')
                                ->label($isArabic ? 'اختر الحالة الجديدة' : 'Select new status')
                                ->options([
                                    'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
                                    'approved' => $isArabic ? 'موافق عليها' : 'Approved',
                                    'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                                    'disabled' => $isArabic ? 'معطلة' : 'Disabled',
                                ])
                                ->required(),
                        ])
                        ->action(function ($record, array $data, FirebaseService $firebase) use ($isArabic): void {
                            $oldStatus = $record->status;
                            $newStatus = $data['status'];

                            // Update the status
                            $record->update(['status' => $newStatus]);

                            // Send notification to vendor if status changed
                            if ($oldStatus !== $newStatus && $record->user && $record->user->fcm_token) {
                                $statusMessages = [
                                    'approved' => [
                                        'title' => 'تم الموافقة على خدمتك!',
                                        'body' => 'مبروك! خدمة "' . $record->name . '" أصبحت نشطة الآن ومتاحة لجميع العملاء. ابدأ باستقبال الطلبات!',
                                        'title_en' => 'Service Approved Successfully!',
                                        'body_en' => 'Congratulations! Your service "' . $record->name . '" is now live and available to all customers. Start receiving orders!',
                                        'status_ar' => 'موافق عليها',
                                        'status_en' => 'Approved',
                                        'color' => '#10B981', // Green
                                        'action_text' => 'عرض الخدمة',
                                        'action_text_en' => 'View Service',
                                    ],
                                    'rejected' => [
                                        'title' => 'تحديث بخصوص خدمتك',
                                        'body' => 'نأسف لإبلاغك أن خدمة "' . $record->name . '" تحتاج لمراجعة. يرجى تحديث البيانات وإعادة التقديم للحصول على الموافقة.',
                                        'title_en' => 'Service Update Required',
                                        'body_en' => 'We regret to inform you that your service "' . $record->name . '" needs review. Please update the information and resubmit for approval.',
                                        'status_ar' => 'بحاجة لمراجعة',
                                        'status_en' => 'Rejected',
                                        'color' => '#EF4444', // Red
                                        'action_text' => 'تعديل الخدمة',
                                        'action_text_en' => 'Edit Service',
                                    ],
                                    'pending' => [
                                        'title' => 'خدمتك قيد المراجعة',
                                        'body' => 'شكراً لتحديث خدمة "' . $record->name . '". فريقنا يقوم بمراجعتها الآن وسنبلغك بالنتيجة قريباً.',
                                        'title_en' => 'Service Under Review',
                                        'body_en' => 'Thank you for updating your service "' . $record->name . '". Our team is reviewing it now and will notify you soon.',
                                        'status_ar' => 'قيد المراجعة',
                                        'status_en' => 'Pending',
                                        'color' => '#F59E0B', // Amber
                                        'action_text' => 'تتبع الحالة',
                                        'action_text_en' => 'Track Status',
                                    ],
                                    'disabled' => [
                                        'title' => 'تم إيقاف خدمتك مؤقتاً',
                                        'body' => 'خدمة "' . $record->name . '" غير متاحة حالياً للعملاء. للمزيد من المعلومات، يرجى التواصل مع الدعم الفني.',
                                        'title_en' => 'Service Temporarily Disabled',
                                        'body_en' => 'Your service "' . $record->name . '" is currently unavailable to customers. For more information, please contact support.',
                                        'status_ar' => 'متوقفة مؤقتاً',
                                        'status_en' => 'Disabled',
                                        'color' => '#6B7280', // Gray
                                        'action_text' => 'اتصل بالدعم',
                                        'action_text_en' => 'Contact Support',
                                    ],
                                ];

                                $message = $statusMessages[$newStatus] ?? null;

                                if ($message) {
                                    try {
                                        // Get user's preferred language (ar or en)
                                        $userLang = $record->user->preferred_language ?? 'ar';

                                        // Select title and body based on user's language
                                        $notificationTitle = $userLang === 'en' ? $message['title_en'] : $message['title'];
                                        $notificationBody = $userLang === 'en' ? $message['body_en'] : $message['body'];

                                        $firebase->sendToDevice(
                                            $record->user->fcm_token,
                                            $notificationTitle,
                                            $notificationBody,
                                            [
                                                'type' => 'vendor_service_status_change',
                                                'service_id' => (string) $record->id,
                                                'service_name' => $record->name,
                                                'old_status' => $oldStatus,
                                                'new_status' => $newStatus,
                                                'status_label_ar' => $message['status_ar'],
                                                'status_label_en' => $message['status_en'],
                                                'color' => $message['color'],
                                                'action_text' => $message['action_text'],
                                                'action_text_en' => $message['action_text_en'],
                                                'title_en' => $message['title_en'],
                                                'body_en' => $message['body_en'],
                                                'timestamp' => now()->toIso8601String(),
                                                'priority' => 'high',
                                            ]
                                        );

                                        Notification::make()
                                            ->success()
                                            ->title($isArabic ? 'تم تحديث الحالة وإرسال الإشعار!' : 'Status updated & notification sent!')
                                            ->body($isArabic ? "تم إشعار البائع {$record->user->name}" : "Vendor {$record->user->name} has been notified")
                                            ->send();
                                    } catch (\Exception $e) {
                                        Notification::make()
                                            ->warning()
                                            ->title($isArabic ? 'تم تحديث الحالة' : 'Status updated')
                                            ->body($isArabic ? 'تم تغيير الحالة لكن فشل إرسال الإشعار: ' . $e->getMessage() : 'Status changed but notification failed: ' . $e->getMessage())
                                            ->send();
                                    }
                                }
                            }
                        })
                        ->successNotificationTitle($isArabic ? 'تم تحديث الحالة بنجاح!' : 'Status updated successfully!'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ]);
    }
}
