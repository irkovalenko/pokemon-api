<?php

namespace App\Actions\Pokemons;

use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class DeletePokemonAction
{
    public function execute(Pokemon $pokemon, User $user): void
    {
        $ownsPokemon = $user->pokemons()->where('pokemons.uuid', $pokemon->uuid)->exists();

        if (!$user->isAdmin() && !$ownsPokemon) {
            abort(403);
        }

        if (!$pokemon->canBeDeletedOrUpdated()) {
            abort(403, 'This pokemon cannot be deleted.');
        }

        if ($pokemon->image_path) {
            Storage::disk('public')->delete($pokemon->image_path);
        }

        if ($pokemon->cry) {
            Storage::disk('public')->delete($pokemon->cry);
        }

        $pokemon->delete();
    }
}
