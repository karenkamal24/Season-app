<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Helpers\LanguageHelper;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        $isArabic = LanguageHelper::isArabic();

        return $schema->components([
            Section::make($isArabic ? 'معلومات التصنيف' : 'Category Information')
                ->description($isArabic ? 'المعلومات الأساسية عن التصنيف' : 'Basic information about the category')
                ->schema([
                    TextInput::make('name_ar')
                        ->label($isArabic ? 'الاسم (عربي)' : 'Arabic Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('name_en')
                        ->label($isArabic ? 'الاسم (إنجليزي)' : 'English Name')
                        ->required()
                        ->maxLength(255),

                    Toggle::make('is_active')
                        ->label($isArabic ? 'نشط' : 'Is Active')
                        ->default(true)
                        ->required(),
                ])
                ->columns(2),

            Section::make($isArabic ? 'الأيقونة' : 'Icon')
                ->icon('heroicon-o-photo')
                ->schema([
                    Placeholder::make('current_icon_preview')
                        ->label($isArabic ? 'الصورة الحالية' : 'Current Icon')
                        ->content(function ($record) {
                            if (!$record || !$record->icon) {
                                return null;
                            }
                            
                            $imageUrl = str_starts_with($record->icon, 'http') 
                                ? $record->icon 
                                : asset('storage/' . $record->icon);
                            
                            return view('filament.forms.components.current-image', [
                                'imageUrl' => $imageUrl
                            ])->render();
                        })
                        ->visible(fn ($record) => $record && $record->icon)
                        ->columnSpanFull(),

                    Toggle::make('remove_icon')
                        ->label($isArabic ? 'حذف الصورة الحالية' : 'Remove Current Icon')
                        ->helperText($isArabic ? 'قم بتفعيل هذا الخيار لحذف الصورة الحالية' : 'Enable this option to remove the current icon')
                        ->visible(fn ($record) => $record && $record->icon)
                        ->default(false)
                        ->dehydrated()
                        ->columnSpanFull(),

                    FileUpload::make('icon')
                        ->label($isArabic ? 'رفع صورة جديدة' : 'Upload New Icon')
                        ->disk('public')
                        ->directory('categories/icons')
                        ->visibility('public')
                        ->image()
                        ->preserveFilenames(false)
                        ->downloadable()
                        ->openable()
                        ->imageEditor()
                        ->imagePreviewHeight('250')
                        ->helperText($isArabic ? 'قم برفع صورة جديدة للأيقونة. إذا قمت بتفعيل خيار "حذف الصورة الحالية" أعلاه، سيتم حذف الصورة القديمة تلقائياً.' : 'Upload a new icon image. If you enabled "Remove Current Icon" above, the old image will be deleted automatically.')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}

