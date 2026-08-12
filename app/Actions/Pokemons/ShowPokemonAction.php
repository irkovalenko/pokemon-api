<?php

namespace App\Actions\Pokemons;

use App\Models\Pokemon;

class ShowPokemonAction
{
    public function execute(Pokemon $pokemon): Pokemon
    {
        return $pokemon->load([
            'abilities.creator',
            'user',
        ]);
    }
}
