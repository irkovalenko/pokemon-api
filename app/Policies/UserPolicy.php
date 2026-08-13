<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin();
    }


    public function delete(User $user, User $model): bool //user performs the action while model is the target of the action
    {
        if (!$user->isAdmin() || $model->isAdmin()) {
            return false;
        }

        if ($model->pokemons()->exists()) {
            return false;
        }

        return true;
    }
}
