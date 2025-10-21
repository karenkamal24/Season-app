<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmergencyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'fire' => $this->fire,
            'police' => $this->police,
            'ambulance' => $this->ambulance,
        ];
    }
}
