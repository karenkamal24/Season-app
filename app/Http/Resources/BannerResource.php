<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class BannerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image' => $this->getImageUrl(),
            'route' => $this->route,
            'language' => $this->language ?? 'ar',
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * Generate final public URL for banner image
     */
    private function getImageUrl(): ?string
    {
        try {
            if (!$this->image) return null;

            // Remove "public/" if present
            $path = str_replace('public/', '', $this->image);

            // Return valid URL like: http://localhost:8000/storage/banners/file.png
            return asset('storage/' . $path);

        } catch (\Exception $e) {
            Log::error("Error generating banner image URL", [
                'banner_id' => $this->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
