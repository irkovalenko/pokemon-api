<?php

namespace App\DataTransferObjects\Pokemons;

use App\DataTransferObjects\Abilities\AbilityData;
use App\Enums\PokemonType;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class PokemonData extends Data
{
    public function __construct(
        public string $name,
        public PokemonType $type,
        public ?string $description,
        #[DataCollectionOf(AbilityData::class)]
        public DataCollection $abilities,
    ) {}
}
