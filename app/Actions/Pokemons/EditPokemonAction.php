<?php

namespace App\Actions\Pokemons;

use App\Models\Pokemon;

class EditPokemonAction
{
    public function execute(Pokemon $pokemon): Pokemon
    {
        return $pokemon->load('abilities.creator');
    }
}
