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

    public function getRecordsFromListing(int $limit = 20, int $offset = 0): array
    {
        $listing = Http::get('https://pokeapi.co/api/v2/pokemon', [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        if (!$listing->successful()) {
            return [];
        }

        $urls = collect($listing->json('results'))->pluck('url');

        $responses = Http::pool(
            fn($pool) => $urls->map(fn($url) => $pool->get($url))->toArray()
        );

        return collect($responses)
            ->filter(fn($res) => $res->successful())
            ->map(fn($res) => $this->transform($res->json()))
            ->values()
            ->toArray();
    }
}
