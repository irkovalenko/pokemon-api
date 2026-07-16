<?php

namespace App\Actions\Abilities;

use App\DataTransferObjects\Abilities\UpdateAbilityData;
use App\Models\Ability;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class UpdateAbilityAction
{
    public function execute(Ability $ability, UpdateAbilityData $data, User $user): Ability
    {
        $ability->loadMissing('creator');

        if (! $ability->canBeEditedBy($user)) {
            throw new AuthorizationException('You are not allowed to edit this ability.');
        }

        $ability->update([
            'name' => $data->name,
            'description' => $data->description,
        ]);

        return $ability->fresh('creator');
    }
}
