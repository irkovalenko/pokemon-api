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
            'comments' => fn($query) => $query->orderBy('created_at', 'desc'),
            'comments.user',
            'comments.replies.user',
        ]);
    }
}
