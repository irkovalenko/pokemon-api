<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->dropForeign(['pokemon_id']);
        });
        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->foreign('pokemon_id')->references('uuid')->on('pokemons')->cascadeOnDelete();
        });

        Schema::table('pokemon_user', function (Blueprint $table) {
            $table->dropForeign(['pokemon_id']);
        });
        Schema::table('pokemon_user', function (Blueprint $table) {
            $table->foreign('pokemon_id')->references('uuid')->on('pokemons')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->dropForeign(['pokemon_id']);
            $table->foreign('pokemon_id')->references('uuid')->on('pokemons');
        });

        Schema::table('pokemon_user', function (Blueprint $table) {
            $table->dropForeign(['pokemon_id']);
            $table->foreign('pokemon_id')->references('uuid')->on('pokemons');
        });
    }
};
