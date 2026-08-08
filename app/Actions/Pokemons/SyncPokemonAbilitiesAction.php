<?php

namespace App\Actions\Pokemons;

use App\DataTransferObjects\Pokemons\AbilityData;
use App\Models\Ability;
use App\Models\Pokemon;
use App\Models\User;
use Spatie\LaravelData\DataCollection;

class SyncPokemonAbilitiesAction
{
    public function execute(Pokemon $pokemon, DataCollection $abilities, User $user): void
    {
        $abilityUuids = [];

        /** @var AbilityData $ability */
        foreach ($abilities as $ability) {
            if (! empty($ability->uuid)) {
                // it is an existing ability, just find it
                $abilityModel = Ability::where('uuid', $ability->uuid)->firstOrFail();
            } else {
                // it is a new ability, create it
                $abilityModel = Ability::firstOrCreate(
                    ['name' => $ability->name],
                    ['description' => $ability->description]
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
