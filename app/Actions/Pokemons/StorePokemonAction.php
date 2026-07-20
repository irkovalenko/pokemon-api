<?php

namespace App\Actions\Pokemons;

use App\DataTransferObjects\Pokemons\StorePokemonData;
use App\Models\Pokemon;
use App\Models\User;

class StorePokemonAction
{
    public function __construct(
        private HandlePokemonCryAndImageAction $fileUploads,
        private SyncPokemonAbilitiesAction $syncAbilities,
    ) {}

    public function execute(StorePokemonData $data, array $files, User $user): Pokemon
    {
        $pokemon = Pokemon::create([
            'name' => toKebabCase($data->name),
            'type' => $data->type,
            'description' => $data->description ? [$data->description] : null,
            'image_path' => $files['image_path'],
            'cry' => $files['cry'],
            'if_banned' => 0,
        ]);

        $this->syncAbilities->execute($pokemon, $data->abilities, $user);

        $pokemon->user()->attach($user->id);

        return $pokemon;
    }
}
