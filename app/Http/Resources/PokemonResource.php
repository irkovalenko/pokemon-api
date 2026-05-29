<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PokemonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image_path' => $this->image_path,
            'cry' => $this->cry,
            'type' => $this->type,
            'if_banned'  => $this->if_banned,
            'abilities' => AbilityResource::collection($this->whenLoaded('abilities')),

        ];
    }
}
