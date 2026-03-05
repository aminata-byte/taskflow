<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL : ALTER COLUMN ... TYPE (pas MODIFY COLUMN comme MySQL)
        DB::statement("ALTER TABLE tasks ALTER COLUMN priority TYPE VARCHAR(10)");

        // Convertir les anciennes valeurs anglaises en français
        DB::statement("UPDATE tasks SET priority = 'basse'   WHERE priority = 'low'");
        DB::statement("UPDATE tasks SET priority = 'moyenne' WHERE priority = 'medium'");
        DB::statement("UPDATE tasks SET priority = 'haute'   WHERE priority = 'high'");

        // Valeur par défaut
        DB::statement("ALTER TABLE tasks ALTER COLUMN priority SET DEFAULT 'basse'");
    }

    public function down(): void
    {
        DB::statement("UPDATE tasks SET priority = 'low'    WHERE priority = 'basse'");
        DB::statement("UPDATE tasks SET priority = 'medium' WHERE priority = 'moyenne'");
        DB::statement("UPDATE tasks SET priority = 'high'   WHERE priority = 'haute'");
        DB::statement("ALTER TABLE tasks ALTER COLUMN priority TYPE VARCHAR(10)");
        DB::statement("ALTER TABLE tasks ALTER COLUMN priority SET DEFAULT 'low'");
    }
};
