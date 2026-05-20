<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $table = 'pokemons';
    protected $fillable = [
        'name',
        'if_banned',
    ];

    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'ability_pokemon');
    }

    public function scopeNotBanned($query)
    {
        return $query->where('if_banned', false);
    }

    public function scopeBanned($query)
    {
        return $query->where('if_banned', true);
    }
}
