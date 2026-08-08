<?php

namespace App\Actions\Pokemons;

use App\DataTransferObjects\Pokemons\PokemonData;
use App\DataTransferObjects\Pokemons\PokemonFilesData;
use App\Models\Pokemon;
use App\Models\User;

class StorePokemonAction
{
    public function __construct(
        private HandlePokemonCryAndImageAction $fileUploads,
        private SyncPokemonAbilitiesAction $syncAbilities,
    ) {}

    public function execute(PokemonFilesData $files, PokemonData $data, User $user): Pokemon
    {
        $uploadedFiles = $this->fileUploads->execute($files);

        $pokemon = Pokemon::create([
            'name' => toKebabCase($data->name),
            'type' => $data->type,
            'description' => $data->description ? [$data->description] : null,
            'image_path' => $uploadedFiles['image_path'],
            'cry' => $uploadedFiles['cry'],
            'if_banned' => 0,
        ]);

        $this->syncAbilities->execute($pokemon, $data->abilities, $user);

        $pokemon->user()->attach($user->id);

        return $pokemon;
    }
}
