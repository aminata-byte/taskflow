<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Étape 1 : Passer en VARCHAR pour lever la restriction ENUM
        DB::statement("ALTER TABLE tasks MODIFY COLUMN priority VARCHAR(10) DEFAULT 'basse'");

        // Étape 2 : Convertir les anciennes valeurs anglaises en français
        DB::statement("UPDATE tasks SET priority = 'basse'   WHERE priority = 'low'");
        DB::statement("UPDATE tasks SET priority = 'moyenne' WHERE priority = 'medium'");
        DB::statement("UPDATE tasks SET priority = 'haute'   WHERE priority = 'high'");

        // Étape 3 : Remettre en ENUM avec les valeurs françaises
        DB::statement("ALTER TABLE tasks MODIFY COLUMN priority ENUM('basse','moyenne','haute') DEFAULT 'basse'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE tasks MODIFY COLUMN priority VARCHAR(10) DEFAULT 'low'");
        DB::statement("UPDATE tasks SET priority = 'low'    WHERE priority = 'basse'");
        DB::statement("UPDATE tasks SET priority = 'medium' WHERE priority = 'moyenne'");
        DB::statement("UPDATE tasks SET priority = 'high'   WHERE priority = 'haute'");
        DB::statement("ALTER TABLE tasks MODIFY COLUMN priority ENUM('low','medium','high') DEFAULT 'low'");
    }
};
