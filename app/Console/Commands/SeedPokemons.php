<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Pokemon;
use App\Services\PokemonService;
use Illuminate\Console\Command;

class SeedPokemons extends Command
{
    protected $signature = 'pokemons:seed {from=1} {to=100}'; // default from to
    protected $description = 'Seed pokemons from PokeAPI into the database';

    public function handle(PokemonService $service)
    {
        $from = $this->argument('from');
        $to = $this->argument('to');

        $this->info("Seeding pokemons {$from} to {$to}...");

        $pokemons = $service->getRecords($from, $to);

        foreach ($pokemons as $pokemon) {
            $pokemonModel = Pokemon::updateOrCreate(
                ['id' => $pokemon['id']],
                [
                    'name'       => $pokemon['name'],
                    'type'       => $pokemon['type'],
                    'image_path' => $pokemon['image_path'],
                    'cry'        => $pokemon['cry'],
                ]
            );

            if ($pokemonModel->wasRecentlyCreated) { // exisiting banned are not overwritten
                $pokemonModel->update(['if_banned' => 0]);
            }

            foreach ($pokemon['abilities'] as $abilityName) {
                $ability = Ability::firstOrCreate(['name' => $abilityName]);
                $pokemonModel->abilities()->syncWithoutDetaching($ability->id);
            }
        }

        $this->info('Done!');
    }
}
