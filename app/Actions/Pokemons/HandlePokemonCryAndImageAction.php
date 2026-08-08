<?php

namespace App\Actions\Pokemons;

use App\DataTransferObjects\Pokemons\PokemonFilesData;

class HandlePokemonCryAndImageAction
{
    /**
     * @return array{image_path: ?string, cry: ?string}
     */
    public function execute(PokemonFilesData $files): array
    {
        $imagePath = $files->image?->store('images/pokemon', 'public');
        $cryPath = $files->cry?->store('cries', 'public');

        return [
            'image_path' => $imagePath,
            'cry' => $cryPath,
        ];
    }
}

// check if there is already such image or add version, overwriting
// images/pokemon/{pokemon_id}
// change to private storing
