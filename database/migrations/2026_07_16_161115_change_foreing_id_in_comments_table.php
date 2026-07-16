<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['pokemon_id']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('pokemon_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->uuid('pokemon_id')->after('content');
            $table->foreign('pokemon_id')->references('uuid')->on('pokemons')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['pokemon_id']);
            $table->dropColumn('pokemon_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedInteger('pokemon_id')->after('content');
            $table->foreign('pokemon_id')->references('id')->on('pokemons');
        });
    }
};
