<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->dropForeign(['pokemon_id']);
            $table->foreign('pokemon_id')->references('id')->on('pokemons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->dropForeign(['pokemon_id']);
            $table->foreign('pokemon_id')->references('id')->on('pokemons');
        });
    }
};
