<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PokemonService
{
    public function transform(array $pokemon, array $abilityDescription = []): array
    {
        return [
            'id' => $pokemon['id'],
            'name' => $pokemon['name'],
            'type' => $pokemon['types'][0]['type']['name'],
            'abilities' => collect($pokemon['abilities'])
                ->map(fn($ability) => [
                    'name' => $ability['ability']['name'],
                    'description' => $abilityDescription[$ability['ability']['name']] ?? null,
                ])
                ->toArray(),
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

        $pokemonPayloads = collect($responses)
            ->filter(fn($res) => $res->successful())
            ->map(fn($res) => $res->json())
            ->values();

        // Collect every unique ability url across all fetched pokemon
        $abilityUrls = $pokemonPayloads
            ->flatMap(fn($p) => collect($p['abilities'])->pluck('ability.url'))
            ->unique()
            ->values();

        $abilityDescription = $this->getAbilityDescription($abilityUrls->toArray());

        return $pokemonPayloads
            ->map(fn($pokemon) => $this->transform($pokemon, $abilityDescription))
            ->values()
            ->toArray();
    }

    /**
     * Pool-fetch ability detail pages and return a [name => short_effect (en)] map.
     */
    protected function getAbilityDescription(array $abilityUrls): array
    {
        if (empty($abilityUrls)) {
            return [];
        }

        $responses = Http::pool(
            fn($pool) => collect($abilityUrls)->map(fn($url) => $pool->get($url))->toArray()
        );

        return collect($responses)
            ->filter(fn($res) => $res->successful())
            ->mapWithKeys(function ($res) {
                $data = $res->json();

                $shortEffect = collect($data['effect_entries'])
                    ->firstWhere('language.name', 'en')['short_effect'] ?? null;

                return [$data['name'] => $shortEffect];
            })
            ->toArray();
    }
}
