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
            ->when($data->type, fn($q) => $q->where('type', $data->type))
            ->when($data->name, fn($q) => $q->where('name', 'like', "%{$data->name}%"))
            ->when($data->user, fn($q) => $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$data->user}%")))
            ->where('if_banned', 0)
            ->with('user')
            ->paginate();
    }
}
