<?php

namespace App\Models;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pokemon extends Model
{
    use HasUuids;
    protected $table = 'pokemons';
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false; //applies to primary key
    protected $fillable = [
        'api_id',
        'name',
        'type',
        'image_path',
        'cry',
        'if_banned',
    ];

    public function abilities(): BelongsToMany
    {
        return $this->belongsToMany(
            Ability::class,
            'ability_pokemon',
            'pokemon_id',
            'ability_id',
            'uuid',
            'uuid'
        );
    }

    public function scopeNotBanned($query)
    {
        return $query->where('if_banned', 0);
    }

    public function scopeBanned($query)
    {
        return $query->where('if_banned', 1);
    }

    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pokemon_user', 'pokemon_id', 'user_id', 'uuid');
    }

    public function canBeDeletedOrUpdated(): bool //only the records in pokemon_user
    {
        return $this->user()->exists();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'pokemon_id', 'uuid');
    }
}
