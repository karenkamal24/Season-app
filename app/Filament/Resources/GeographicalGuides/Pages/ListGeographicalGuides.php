<?php

namespace App\Filament\Resources\GeographicalGuides\Pages;

use App\Filament\Resources\GeographicalGuides\GeographicalGuideResource;
use App\Exports\GeographicalGuidesTemplateExport;
use App\Imports\GeographicalGuidesImport;
use App\Helpers\LanguageHelper;
use App\Models\Country;
use App\Models\City;
use App\Models\GeographicalCategory;
use App\Models\GeographicalSubCategory;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListGeographicalGuides extends ListRecords
{
    protected static string $resource = GeographicalGuideResource::class;

    protected function getHeaderActions(): array
    {
        $isArabic = LanguageHelper::isArabic();

        return [
            Actions\CreateAction::make(),
            Action::make('download_template')
                ->label($isArabic ? 'تحميل قالب Excel' : 'Download Excel Template')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return Excel::download(new GeographicalGuidesTemplateExport(), 'geographical_guides_template.xlsx');
                }),
            Action::make('import')
                ->label($isArabic ? 'استيراد من Excel' : 'Import from Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    Select::make('country_id')
                        ->label($isArabic ? 'الدولة' : 'Country')
                        ->options(Country::all()->pluck($isArabic ? 'name_ar' : 'name_en', 'id'))
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('city_id', null)),
                    
                    Select::make('city_id')
                        ->label($isArabic ? 'المدينة' : 'City')
                        ->options(function (callable $get) use ($isArabic) {
                            $countryId = $get('country_id');
                            if (!$countryId) {
                                return [];
                            }
                            return City::where('country_id', $countryId)
                                ->pluck($isArabic ? 'name_ar' : 'name_en', 'id');
                        })
                        ->searchable()
                        ->required()
                        ->reactive(),
                    
                    Select::make('geographical_category_id')
                        ->label($isArabic ? 'التصنيف' : 'Category')
                        ->options(GeographicalCategory::all()->pluck($isArabic ? 'name_ar' : 'name_en', 'id'))
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('geographical_sub_category_id', null)),
                    
                    Select::make('geographical_sub_category_id')
                        ->label($isArabic ? 'التصنيف الفرعي' : 'Sub Category')
                        ->options(function (callable $get) use ($isArabic) {
                            $categoryId = $get('geographical_category_id');
                            if (!$categoryId) {
                                return [];
                            }
                            return GeographicalSubCategory::where('geographical_category_id', $categoryId)
                                ->pluck($isArabic ? 'name_ar' : 'name_en', 'id');
                        })
                        ->searchable()
                        ->nullable()
                        ->reactive(),
                    
                    FileUpload::make('file')
                        ->label($isArabic ? 'ملف Excel' : 'Excel File')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                        ->required(),
                ])
                ->action(function (array $data) use ($isArabic) {
                    try {
                        Excel::import(
                            new GeographicalGuidesImport(
                                $data['country_id'],
                                $data['city_id'],
                                $data['geographical_category_id'],
                                $data['geographical_sub_category_id'] ?? null
                            ),
                            $data['file']
                        );

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



