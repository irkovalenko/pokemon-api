<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Swap primary key from id to uuid
        DB::statement('ALTER TABLE abilities MODIFY id BIGINT UNSIGNED NOT NULL, DROP PRIMARY KEY');
        DB::statement('ALTER TABLE abilities ADD UNIQUE (id)');
        DB::statement('ALTER TABLE abilities ADD PRIMARY KEY (uuid)');

        // 2. Restore AUTO_INCREMENT on id (this step silently got lost during the PK swap last time with pokemons)
        DB::statement('ALTER TABLE abilities MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');

        // 3. Convert ability_pokemon.ability_id -> uuid
        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->uuid('ability_uuid')->nullable();
        });

        DB::statement('
            UPDATE ability_pokemon ap
            JOIN abilities a ON a.api_id = ap.ability_id
            SET ap.ability_uuid = a.uuid
        ');

        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->dropColumn('ability_id');
            $table->renameColumn('ability_uuid', 'ability_id');
        });

        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->uuid('ability_id')->nullable(false)->change();
        });

        // 4. Recreate FK, now pointing at abilities.uuid
        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->foreign('ability_id')->references('uuid')->on('abilities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Rollback not implemented. UUID migration should be reversed with a data migration.'
        );
    }
};
