<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackingTipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'tip_id' => $this->id,
            'text' => $this->text_en,
            'text_arabic' => $this->text_ar,
            'category' => $this->category,
            'priority' => $this->priority,
        ];
    }
}
