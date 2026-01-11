<?php

namespace App\Filament\Resources\GeographicalGuides\Tables;

use App\Helpers\LanguageHelper;
use App\Services\FirebaseService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;

class GeographicalGuidesTable
{
    public static function configure(Table $table): Table
    {
        $isArabic = LanguageHelper::isArabic();

        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label($isArabic ? 'المستخدم' : 'User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('service_name')
                    ->label($isArabic ? 'اسم الخدمة' : 'Service Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name_ar')
                    ->label($isArabic ? 'التصنيف' : 'Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subCategory.name_ar')
                    ->label($isArabic ? 'التصنيف الفرعي' : 'Sub Category')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('country.name_ar')
                    ->label($isArabic ? 'الدولة' : 'Country')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city.name_ar')
                    ->label($isArabic ? 'المدينة' : 'City')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone_1')
                    ->label($isArabic ? 'رقم الموبايل 1' : 'Phone 1')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone_2')
                    ->label($isArabic ? 'رقم الموبايل 2' : 'Phone 2')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label($isArabic ? 'نشط' : 'Active'),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'pending' => $isArabic ? 'قيد المراجعة' : 'Pending',
                        'approved' => $isArabic ? 'موافق عليها' : 'Approved',
                        'rejected' => $isArabic ? 'مرفوضة' : 'Rejected',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->label($isArabic ? 'الحالة' : 'Status')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label($isArabic ? 'تاريخ الإنشاء' : 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label($isArabic ? 'تاريخ التحديث' : 'Updated At')
                    ->dateTime()
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
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
                            ])
                            ->required()
                            ->default(fn ($record) => $record->status),
                    ])
                    ->action(function ($record, array $data, FirebaseService $firebase) use ($isArabic): void {
                        $oldStatus = $record->status;
                        $newStatus = $data['status'];

                        // Update the status
                        $record->update(['status' => $newStatus]);

                        // Send notification to user if status changed
                        if ($oldStatus !== $newStatus && $record->user && $record->user->fcm_token) {
                            $statusMessages = [
                                'approved' => [
                                    'title' => 'تم الموافقة على دليلك الجغرافي!',
                                    'body' => 'مبروك! دليلك الجغرافي "' . $record->service_name . '" أصبح نشطاً الآن ومتاحاً لجميع المستخدمين.',
                                    'title_en' => 'Geographical Guide Approved Successfully!',
                                    'body_en' => 'Congratulations! Your geographical guide "' . $record->service_name . '" is now live and available to all users.',
                                    'status_ar' => 'موافق عليها',
                                    'status_en' => 'Approved',
                                    'color' => '#10B981', // Green
                                    'action_text' => 'عرض الدليل',
                                    'action_text_en' => 'View Guide',
                                ],
                                'rejected' => [
                                    'title' => 'تحديث بخصوص دليلك الجغرافي',
                                    'body' => 'نأسف لإبلاغك أن دليلك الجغرافي "' . $record->service_name . '" يحتاج لمراجعة. يرجى تحديث البيانات وإعادة التقديم للحصول على الموافقة.',
                                    'title_en' => 'Geographical Guide Update Required',
                                    'body_en' => 'We regret to inform you that your geographical guide "' . $record->service_name . '" needs review. Please update the information and resubmit for approval.',
                                    'status_ar' => 'مرفوضة',
                                    'status_en' => 'Rejected',
                                    'color' => '#EF4444', // Red
                                    'action_text' => 'تعديل الدليل',
                                    'action_text_en' => 'Edit Guide',
                                ],
                                'pending' => [
                                    'title' => 'دليلك الجغرافي قيد المراجعة',
                                    'body' => 'شكراً لتحديث دليلك الجغرافي "' . $record->service_name . '". فريقنا يقوم بمراجعته الآن وسنبلغك بالنتيجة قريباً.',
                                    'title_en' => 'Geographical Guide Under Review',
                                    'body_en' => 'Thank you for updating your geographical guide "' . $record->service_name . '". Our team is reviewing it now and will notify you soon.',
                                    'status_ar' => 'قيد المراجعة',
                                    'status_en' => 'Pending',
                                    'color' => '#F59E0B', // Amber
                                    'action_text' => 'تتبع الحالة',
                                    'action_text_en' => 'Track Status',
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
                                            'type' => 'geographical_guide_status_change',
                                            'guide_id' => (string) $record->id,
                                            'guide_name' => $record->service_name,
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
                                        ->body($isArabic ? "تم إشعار المستخدم {$record->user->name}" : "User {$record->user->name} has been notified")
                                        ->send();
                                } catch (\Exception $e) {
                                    Notification::make()
                                        ->warning()
                                        ->title($isArabic ? 'تم تحديث الحالة' : 'Status updated')
                                        ->body($isArabic ? 'تم تغيير الحالة لكن فشل إرسال الإشعار: ' . $e->getMessage() : 'Status changed but notification failed: ' . $e->getMessage())
                                        ->send();
                                }
                            }
                        } else {
                            // Status updated but no notification needed (no user, no FCM token, or status didn't change)
                            Notification::make()
                                ->success()
                                ->title($isArabic ? 'تم تحديث الحالة بنجاح!' : 'Status updated successfully!')
                                ->send();
                        }
                    }),
                DeleteAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

