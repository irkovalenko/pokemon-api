<?php

namespace App\DataTransferObjects\Pokemons;

use App\DataTransferObjects\Abilities\AbilityData;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class PokemonData extends Data
{
    public function __construct(
        public string $name,
        public string $type,
        public ?string $description,
        #[DataCollectionOf(AbilityData::class)]
        public DataCollection $abilities,
    ) {}
}
