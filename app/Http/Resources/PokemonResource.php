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
            'uuid' => $this->uuid,
            'name' => $this->name,
            'image_path' => $this->formatFileUrl($this->image_path),
            'cry' => $this->formatFileUrl($this->cry),
            'type' => $this->type,
            'if_banned'  => $this->if_banned,
            'abilities' => AbilityResource::collection($this->whenLoaded('abilities')),
            'comments' => CommentResource::collection($this->whenLoaded('comments', fn() => $this->comments->whereNull('parent_id'))),
            'user' => $this->whenLoaded('user', fn() => $this->user->first()?->name),

        ];
    }


    private function formatFileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::url($path);
    }
}
