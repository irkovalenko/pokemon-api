<?php

namespace App\Actions\Pokemons;

use App\Models\Ability;
use App\Models\Pokemon;
use App\Models\User;

class SyncPokemonAbilitiesAction
{
    public function execute(Pokemon $pokemon, array $abilities, User $user): void
    {
        $abilityUuids = [];

        foreach ($abilities as $ability) {
            if (! empty($ability['uuid'])) {
                $abilityModel = Ability::where('uuid', $ability['uuid'])->firstOrFail();
            } else {
                $abilityModel = Ability::firstOrCreate(
                    ['name' => $ability['name']],
                    ['description' => $ability['description'] ?? null]
                );

                if ($abilityModel->wasRecentlyCreated) {
                    $abilityModel->creator()->attach($user->id);
                }
            }

            $abilityUuids[] = $abilityModel->uuid;
        }

        $pokemon->abilities()->sync($abilityUuids);
    }
}
