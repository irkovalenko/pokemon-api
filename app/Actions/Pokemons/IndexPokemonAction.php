<?php

namespace App\Actions\Pokemons;

use App\DataTransferObjects\Pokemons\FilterPokemonData;
use App\Models\Pokemon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class IndexPokemonAction
{
    public function execute(FilterPokemonData $data): LengthAwarePaginator
    {
        return Pokemon::query()
            ->when($data->type, fn(Builder $q): Builder => $q->where('type', $data->type))
            ->when($data->name, function (Builder $query) use ($data): Builder {
                $name = $data->name;

                return $query->where(function (Builder $q) use ($name): Builder {
                    return $q->where('name', 'like', "%{$name}%")
                        ->orWhereHas('user', function (Builder $q) use ($name): Builder {
                            return $q->where('name', 'like', "%{$name}%");
                        });
                });
            })
            ->where('if_banned', 0)
            ->with('user')
            ->paginate();
    }
}
