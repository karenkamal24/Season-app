<?php

namespace App\Filament\Resources\CategoryApps\Pages;

use App\Filament\Resources\CategoryApps\CategoryAppResource;
use App\Exports\CategoryAppsTemplateExport;
use App\Imports\CategoryAppsImport;
use App\Helpers\LanguageHelper;
use App\Models\Category;
use App\Models\Country;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListCategoryApps extends ListRecords
{
    protected static string $resource = CategoryAppResource::class;

    protected function getHeaderActions(): array
    {
        $isArabic = LanguageHelper::isArabic();

        return [
            CreateAction::make(),
            Action::make('download_template')
                ->label($isArabic ? 'تحميل قالب Excel' : 'Download Excel Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(new CategoryAppsTemplateExport(), 'category_apps_template.xlsx');
                }),
            Action::make('import')
                ->label($isArabic ? 'استيراد من Excel' : 'Import from Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Select::make('category_id')
                        ->label($isArabic ? 'التصنيف' : 'Category')
                        ->options(Category::all()->pluck('name_ar', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('country_ids')
                        ->label($isArabic ? 'الدول' : 'Countries')
                        ->options(Country::all()->pluck('name_ar', 'id'))
                        ->searchable()
                        ->multiple()
                        ->required(),
                    FileUpload::make('file')
                        ->label($isArabic ? 'ملف Excel' : 'Excel File')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required(),
                ])
                ->action(function (array $data) use ($isArabic) {
                    try {
                        Excel::import(new CategoryAppsImport($data['category_id'], $data['country_ids']), $data['file']);

                        Notification::make()
                            ->title($isArabic ? 'تم الاستيراد بنجاح' : 'Import Successful')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title($isArabic ? 'فشل الاستيراد' : 'Import Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}

