<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'api_id' => $this->api_id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'canEdit' => $this->canBeEditedBy($request->user()),
        ];
    }
}
