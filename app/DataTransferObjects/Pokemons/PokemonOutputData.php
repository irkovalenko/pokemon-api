<?php

namespace App\DataTransferObjects\Pokemons;

use App\DataTransferObjects\Abilities\AbilityData;
use App\Enums\PokemonType;
use App\Models\Pokemon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class PokemonOutputData extends Data
{
    public function __construct(
        public string $uuid,
        public string $name,
        public PokemonType $type,
        public ?array $description,
        public ?string $imageUrl,
        public ?string $cryUrl,
        public bool $ifBanned,

        #[DataCollectionOf(AbilityData::class)]
        public DataCollection $abilities,

        public ?string $userName,
    ) {}

    public static function fromModel(Pokemon $pokemon): self
    {
        return new self(
            uuid: $pokemon->uuid,
            name: $pokemon->name,
            type: $pokemon->type,
            description: $pokemon->description ?? null,
            imageUrl: self::resolveUrl($pokemon->image_path, 'pokemons.image', $pokemon->uuid),
            cryUrl: self::resolveUrl($pokemon->cry, 'pokemons.cry', $pokemon->uuid),
            ifBanned: (bool) $pokemon->if_banned,
            abilities: AbilityData::collect($pokemon->abilities, DataCollection::class),
            userName: $pokemon->user->first()?->name,
        );
    }

    private static function resolveUrl(?string $path, string $routeName, string $uuid): ?string
    {
        if (! $path) {
            return null;
        }

        return str_starts_with($path, 'http')
            ? $path
            : route($routeName, $uuid);
    }
}
