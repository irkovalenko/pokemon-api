<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ability extends Model
{
    protected $table = 'abilities';
    protected $fillable = [
        'name'
    ];

    public function pokemons(): BelongsToMany
    {
        return $this->belongsToMany(Pokemon::class, 'ability_pokemon');
    }
}
