<?php

namespace App\Services;

use App\Models\Pokemon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class PokemonDescriptionService
{
    public function getPokemonDescriptions(Pokemon $pokemon): Collection
    {
        if (! $pokemon->api_id) {
            return collect();
        }

        $response = Http::get("https://pokeapi.co/api/v2/pokemon-species/{$pokemon->api_id}");

        if (! $response->successful()) {
            return collect();
        }

        $data = $response->json();

        return collect($data['flavor_text_entries'] ?? [])
            ->where('language.name', 'en')
            ->pluck('flavor_text')
            ->map(fn($text) => preg_replace('/\s+/', ' ', trim($text))) // collape any escape character
            // same visual text, different invisible characters problem
            ->unique()
            ->values();
    }
}
