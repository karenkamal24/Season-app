<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserQrResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'coins'=> $this->coins,
            'photo_url'   => $this->photo_url
                ? asset('storage/' . ltrim($this->photo_url, '/'))
                : null,
            'qr_code_url' => $this->qr_code_url ?? null,
        ];
    }
}
