<?php

namespace App\Actions\Pokemons;

use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class ToggleBanPokemonAction
{

    public function execute(Pokemon $pokemon, ?User $user): Pokemon
    {
        if (!$user?->isAdmin()) {
            throw new AuthorizationException();
        }
        $pokemon->update([
            'if_banned' => ! $pokemon->if_banned
        ]);

        return $pokemon;
    }
}
