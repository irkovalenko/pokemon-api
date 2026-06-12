<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PokemonService
{
    //extracting the data from api call

    public function transform(array $pokemon): array
    {
        return [
            'id' => $pokemon['id'],
            'name' => $pokemon['name'],
            'type' => $pokemon['types'][0]['type']['name'],
            'abilities' => collect($pokemon['abilities'])->pluck('ability.name')->toArray(),
            'image_path' => $pokemon['sprites']['other']['official-artwork']['front_default'],
            'cry' => $pokemon['cries']['latest'],
        ];
    }

    public function getRecords(int $from, int $to): array
    {
        $responses = Http::pool( //pool fetches multiple pokemons at the time, not one by one
            fn($pool) => collect(range($from, $to))
                ->map(fn($id) => $pool->get("https://pokeapi.co/api/v2/pokemon/{$id}/"))
                ->toArray()
        );

        return collect($responses)
            ->filter(fn($res) => $res->successful())
            ->map(fn($res) => $this->transform($res->json()))
            ->values()
            ->toArray();
    }
}
