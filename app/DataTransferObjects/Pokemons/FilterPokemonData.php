<?php

namespace App\DataTransferObjects\Pokemons;

readonly class FilterPokemonData
{
    public function __construct(
        public ?string $type = null,
        public ?string $name = null,
        public ?string $user = null,
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request): self
    {
        return new self(
            type: $request->input('type'),
            name: $request->input('name'),
            user: $request->input('user'),
        );
    }
}
