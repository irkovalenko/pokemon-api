<?php

namespace App\Console\Commands;

use App\Models\Pokemon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('update-pokemon-names-to-kebab')]
#[Description('Command description')]
class UpdatePokemonNamesToKebab extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Pokemon::whereIn('id', DB::table('pokemon_user')->pluck('pokemon_id'))
            ->each(function ($pokemon) {
                $pokemon->update([
                    'name' => toKebabCase($pokemon->name),
                ]);
            });

        $this->info('Pokemon names updated to kebab-case successfully!');
    }
}
