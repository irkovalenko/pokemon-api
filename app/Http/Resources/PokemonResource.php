<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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
            'image_path' => $this->image_path
                ? (str_starts_with($this->image_path, 'http') ? $this->image_path : Storage::url($this->image_path))
                : null,

            'cry' => $this->cry
                ? (str_starts_with($this->cry, 'http') ? $this->cry : Storage::url($this->cry))
                : null,
            'type' => $this->type,
            'if_banned'  => $this->if_banned,
            'abilities' => AbilityResource::collection($this->whenLoaded('abilities')),
            'user' => $this->whenLoaded('user', fn() => $this->user->first()?->name),

        ];
    }
}
