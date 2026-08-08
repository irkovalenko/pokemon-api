<?php

namespace App\Actions\Pokemons;

use App\DataTransferObjects\Pokemons\FilterPokemonData;
use App\Models\Pokemon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class IndexPokemonAction
{
    public function execute(FilterPokemonData $data): LengthAwarePaginator
    {
        return Pokemon::query()
            ->when($data->type, fn($q) => $q->where('type', $data->type)) // builder syntax and for return fn(Buider $q): Builder
            ->when($data->name, fn($q) => $q->where('name', 'like', "%{$data->name}%"))
            ->when($data->user, fn($q) => $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$data->user}%"))) // or for name and user
            ->where('if_banned', 0)
            ->with('user')
            ->paginate();
    }
}

// use the search property to find the match either in name column or in the user column so there is only one search field in frontend