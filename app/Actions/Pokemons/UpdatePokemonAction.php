<?php

namespace App\Actions\Pokemons;

use App\DataTransferObjects\Pokemons\UpdatePokemonData;
use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class UpdatePokemonAction
{
    public function __construct(
        private SyncPokemonAbilitiesAction $syncAbilities,
    ) {}

    public function execute(Pokemon $pokemon, UpdatePokemonData $data, array $files, User $user): Pokemon
    {
        if (! $pokemon->canBeDeletedOrUpdated()) {
            throw new AuthorizationException('This pokemon cannot be updated.');
        }

        $pokemon->update([
            'name' => toKebabCase($data->name),
            'type' => $data->type,
            'description' => $data->description ? [$data->description] : $pokemon->description,
            'image_path' => $files['image_path'] ?? $pokemon->image_path,
            'cry' => $files['cry'] ?? $pokemon->cry,
        ]);

        $this->syncAbilities->execute($pokemon, $data->abilities, $user);

        return $pokemon;
    }
}
