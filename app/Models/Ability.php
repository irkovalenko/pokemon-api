<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    protected $table = 'abilities';
    protected $fillable = [
        'name'
    ];

    public function pokemons()
    {
        return $this->belongsToMany(Pokemon::class, 'ability_pokemon');
    }
}
