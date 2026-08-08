<?php

namespace App\DataTransferObjects\Pokemons;

use App\Enums\PokemonType;

readonly class FilterPokemonData
{
    public function __construct(
        public ?PokemonType $type = null,
        public ?string $name = null, // combine name and user into one search property
        public ?string $user = null,
    ) {}

    public static function fromRequest(\Illuminate\Http\Request $request): self //just from and do not pass the request but only the validated array from the request
    {
        return new self(
            type: $request->enum('type', PokemonType::class),
            name: $request->input('name'),
            user: $request->input('user'),
        );
    }
}
