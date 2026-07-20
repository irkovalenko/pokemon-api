<?php

namespace App\Console\Commands;

use App\Models\Pokemon;
use App\Services\PokemonDescriptionService;
use Illuminate\Console\Command;

class FillPokemonDescription extends Command
{
    protected $signature = 'pokemon:fill-descriptions';
    protected $description = 'Fetch and store descriptions for pokemon sourced from PokeAPI';

    public function handle(PokemonDescriptionService $service): void
    {
        $pokemons = Pokemon::whereNotNull('api_id')->whereNull('description')->get();

        $this->info("Found {$pokemons->count()} pokemon needing descriptions.");

        foreach ($pokemons as $pokemon) {
            $descriptions = $service->getPokemonDescriptions($pokemon)->unique()->values();

            if ($descriptions->isNotEmpty()) {
                $pokemon->update(['description' => $descriptions]);
                $this->line("✓ {$pokemon->name} ({$descriptions->count()} entries)");
            } else {
                $this->warn("✗ {$pokemon->name} — no description found");
            }
        }

        $this->info('Done.');
    }
}
