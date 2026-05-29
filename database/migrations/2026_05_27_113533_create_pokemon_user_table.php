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
        Schema::create('pokemon_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('pokemon_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreign('pokemon_id')->references('id')->on('pokemons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokemon_user');
    }
};
