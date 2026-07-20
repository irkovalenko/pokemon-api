<?php

namespace App\DataTransferObjects\Pokemons;

readonly class UpdatePokemonData
{
    public function __construct(
        public string $name,
        public string $type,
        public ?string $description,
        public array $abilities,
    ) {}

    public static function fromArray(array $validated): self
    {
        return new self(
            name: $validated['name'],
            type: $validated['type'],
            description: $validated['description'] ?? null,
            abilities: $validated['abilities'],
        );
    }
}
