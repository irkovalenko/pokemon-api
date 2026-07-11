<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Pokemon;
use App\Services\PokemonService;
use Illuminate\Console\Command;

class FillDatabaseWithApiPokemonsData extends Command
{
    protected $signature = 'pokemons:fill {from=1} {to=100} {--fresh : Wipe existing pokemons before seeding}';
    protected $description = 'Fills the database with the data from PokeAPI';

    public function handle(PokemonService $service)
    {
        $from = (int) $this->argument('from');
        $to = (int) $this->argument('to');

        if ($this->option('fresh')) {
            $this->info('Clearing existing pokemons...');
            Pokemon::query()->delete();
        }

        $this->info("Seeding pokemons {$from} to {$to}...");

        $limit = $to - $from + 1;
        $offset = $from - 1;

        $pokemons = $service->getRecordsFromListing($limit, $offset);

        foreach ($pokemons as $pokemon) {
            $pokemonModel = Pokemon::updateOrCreate(
                ['api_id' => $pokemon['id']],
                [
                    'name'       => $pokemon['name'],
                    'type'       => $pokemon['type'],
                    'image_path' => $pokemon['image_path'],
                    'cry'        => $pokemon['cry'],
                ]
            );

            foreach ($pokemon['abilities'] as $ability) {
                $abilityModel = Ability::firstOrCreate(
                    ['name' => $ability['name']],
                    ['description' => $ability['description']]
                );

                // if the ability already existed without a description, backfill it
                if (!$abilityModel->description && $ability['description']) {
                    $abilityModel->update(['description' => $ability['description']]);
                }

                $pokemonModel->abilities()->syncWithoutDetaching($abilityModel->uuid);
            }
        }

        $this->info('Done!');
    }
}
