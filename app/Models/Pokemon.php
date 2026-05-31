<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    protected $table = 'pokemons';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = [
        'id',
        'name',
        'type',
        'image_path',
        'cry',
        'if_banned',
    ];

    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'ability_pokemon');
    }

    public function scopeNotBanned($query)
    {
        return $query->where('if_banned', 0);
    }

    public function scopeBanned($query)
    {
        return $query->where('if_banned', 1);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function canBeDeletedOrUpdated(): bool //only the records in pokemon_user
    {
        return $this->users()->exists();
    }
}
