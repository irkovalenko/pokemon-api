<?php

namespace App\DataTransferObjects\Comments;

readonly class StoreCommentData
{
    public function __construct(
        public string $pokemonId,
        public int $userId,
        public string $content,
        public ?string $parentId = null,
    ) {}

    public static function fromArray(array $validated, int $userId): self
    {
        return new self(
            pokemonId: $validated['pokemon_id'],
            userId: $userId,
            content: $validated['content'],
            parentId: $validated['parent_id'] ?? null,
        );
    }
}
