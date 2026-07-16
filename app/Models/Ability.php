<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ability extends Model
{
    use HasUuids;

    protected $table = 'abilities';
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'api_id',
        'name',
        'description'
    ];

    public function pokemons(): BelongsToMany
    {
        return $this->belongsToMany(
            Pokemon::class,
            'ability_pokemon',
            'pokemon_id',
            'ability_id',
            'uuid',
            'uuid'
        );
    }

    public function creator(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'ability_user',
            'ability_id',
            'user_id',
            'uuid',
            'id'
        );
    }

    public function isCreatedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->creator->contains('id', $user->id);
    }

    public function canBeEditedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // abilities coming from api cannot be edited
        if ($this->creator->isEmpty()) {
            return false;
        }

        return $user->isAdmin() || $this->isCreatedBy($user);
    }
}
