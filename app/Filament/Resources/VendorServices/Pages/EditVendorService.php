<?php

namespace App\Filament\Resources\VendorServices\Pages;

use App\Filament\Resources\VendorServices\VendorServiceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditVendorService extends EditRecord
{
    protected static string $resource = VendorServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // الحصول على القيم الأصلية من قاعدة البيانات مباشرة
        // متجاوزين الـ accessor الذي يحول المسارات إلى URLs
        if ($this->record && $this->record->exists) {
            $rawValue = $this->record->getRawOriginal('images');
            if (!is_null($rawValue)) {
                $images = is_array($rawValue) ? $rawValue : json_decode($rawValue, true);
                if (is_array($images)) {
                    $data['images'] = array_filter($images);
                }
            } else {
                $data['images'] = [];
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // التأكد من أن الصور محفوظة كمسارات نسبية فقط
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = array_map(function ($image) {
                // إذا كان URL كامل، نحوله إلى مسار نسبي
                if (is_string($image) && str_starts_with($image, 'http')) {
                    $path = parse_url($image, PHP_URL_PATH);
                    if ($path && str_starts_with($path, '/storage/')) {
                        return str_replace('/storage/', '', $path);
                    }
                    // محاولة أخرى: إذا كان يحتوي على asset('storage/')
                    if (str_contains($image, '/storage/')) {
                        $parts = explode('/storage/', $image);
                        if (isset($parts[1])) {
                            return $parts[1];
                        }
                    }
                }
                // إذا كان مسار نسبي، نعيده كما هو
                return $image;
            }, array_filter($data['images']));
        }

        return $data;
    }
}
