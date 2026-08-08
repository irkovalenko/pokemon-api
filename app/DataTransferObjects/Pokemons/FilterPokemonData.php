<?php

namespace App\DataTransferObjects\Pokemons;

use App\Enums\PokemonType;
use Spatie\LaravelData\Data;

class FilterPokemonData extends Data
{
    public function __construct(
        public ?PokemonType $type = null,
        public ?string $name = null,
    ) {}
}
