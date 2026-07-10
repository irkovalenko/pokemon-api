<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function dropIndexIfExists(string $table, string $index): void
    {
        $exists = DB::select("SHOW INDEX FROM `$table` WHERE Key_name = ?", [$index]);
        if (!empty($exists)) {
            DB::statement("ALTER TABLE `$table` DROP INDEX `$index`");
        }
    }

    public function up(): void
    {
        /*
         * 1. Remove any stale indexes tied to the old pokemon_id columns, if present
         */
        $this->dropIndexIfExists('pokemon_user', 'pokemon_user_pokemon_id_foreign');
        $this->dropIndexIfExists('comments', 'comments_pokemon_id_foreign');
        $this->dropIndexIfExists('ability_pokemon', 'ability_pokemon_pokemon_id_foreign');

        /*
         * 2. Swap primary key from id to uuid.
         * (id / api_id / uuid columns already exist and are populated
         * from earlier partial runs — nothing to add here.)
         */
        DB::statement('ALTER TABLE pokemons MODIFY id BIGINT UNSIGNED NOT NULL, DROP PRIMARY KEY');
        DB::statement('ALTER TABLE pokemons ADD UNIQUE (id)');
        DB::statement('ALTER TABLE pokemons ADD PRIMARY KEY (uuid)');

        /*
         * 3. Convert comments.pokemon_id -> uuid
         */
        Schema::table('comments', function (Blueprint $table) {
            $table->uuid('pokemon_uuid')->nullable();
        });

        DB::statement('
            UPDATE comments c
            JOIN pokemons p ON p.api_id = c.pokemon_id
            SET c.pokemon_uuid = p.uuid
        ');

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('pokemon_id');
            $table->renameColumn('pokemon_uuid', 'pokemon_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->uuid('pokemon_id')->nullable(false)->change();
        });

        /*
         * 4. Convert pokemon_user.pokemon_id -> uuid
         */
        Schema::table('pokemon_user', function (Blueprint $table) {
            $table->uuid('pokemon_uuid')->nullable();
        });

        DB::statement('
            UPDATE pokemon_user pu
            JOIN pokemons p ON p.api_id = pu.pokemon_id
            SET pu.pokemon_uuid = p.uuid
        ');

        Schema::table('pokemon_user', function (Blueprint $table) {
            $table->dropColumn('pokemon_id');
            $table->renameColumn('pokemon_uuid', 'pokemon_id');
        });

        Schema::table('pokemon_user', function (Blueprint $table) {
            $table->uuid('pokemon_id')->nullable(false)->change();
        });

        /*
         * 5. Convert ability_pokemon.pokemon_id -> uuid
         */
        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->uuid('pokemon_uuid')->nullable();
        });

        DB::statement('
            UPDATE ability_pokemon ap
            JOIN pokemons p ON p.api_id = ap.pokemon_id
            SET ap.pokemon_uuid = p.uuid
        ');

        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->dropColumn('pokemon_id');
            $table->renameColumn('pokemon_uuid', 'pokemon_id');
        });

        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->uuid('pokemon_id')->nullable(false)->change();
        });

        /*
         * 6. Restore foreign keys, now pointing at pokemons.uuid
         */
        Schema::table('comments', function (Blueprint $table) {
            $table->foreign('pokemon_id')->references('uuid')->on('pokemons')->cascadeOnDelete();
        });

        Schema::table('pokemon_user', function (Blueprint $table) {
            $table->foreign('pokemon_id')->references('uuid')->on('pokemons');
        });

        Schema::table('ability_pokemon', function (Blueprint $table) {
            $table->foreign('pokemon_id')->references('uuid')->on('pokemons');
        });
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Rollback not implemented. UUID migration should be reversed with a data migration.'
        );
    }
};
