<?php

namespace App\Actions\Pokemons;

use App\DataTransferObjects\Pokemons\PokemonData;
use App\DataTransferObjects\Pokemons\PokemonFilesData;
use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class UpdatePokemonAction
{
    public function __construct(
        private SyncPokemonAbilitiesAction $syncAbilities,
        private HandlePokemonCryAndImageAction $fileUploads,
    ) {}

    public function execute(Pokemon $pokemon, PokemonData $data, PokemonFilesData $files, User $user): Pokemon
    {
        if (! $pokemon->canBeDeletedOrUpdated()) {
            throw new AuthorizationException('This pokemon cannot be updated.');
        }

        $uploadedFiles = $this->fileUploads->execute($files, $pokemon->uuid);

        $pokemon->update([
            'name' => toKebabCase($data->name),
            'type' => $data->type,
            'description' => $data->description ? [$data->description] : $pokemon->description,
            'image_path' => $uploadedFiles['image_path'] ?? $pokemon->image_path,
            'cry' => $uploadedFiles['cry'] ?? $pokemon->cry,
        ]);

        $this->syncAbilities->execute($pokemon, $data->abilities, $user);

        return $pokemon;
    }
}
