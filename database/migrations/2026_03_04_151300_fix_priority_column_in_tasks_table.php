<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cette migration corrigeait les données locales MySQL (low/medium/high → basse/moyenne/haute)
        // Sur PostgreSQL (Render), la table est fraîche donc on vérifie d'abord
        // si des anciennes valeurs existent avant de modifier

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL — juste convertir si des anciennes valeurs existent
            DB::statement("UPDATE tasks SET priority = 'basse'   WHERE priority = 'low'");
            DB::statement("UPDATE tasks SET priority = 'moyenne' WHERE priority = 'medium'");
            DB::statement("UPDATE tasks SET priority = 'haute'   WHERE priority = 'high'");
        } else {
            // MySQL — syntaxe MODIFY COLUMN
            DB::statement("ALTER TABLE tasks MODIFY COLUMN priority VARCHAR(10) DEFAULT 'basse'");
            DB::statement("UPDATE tasks SET priority = 'basse'   WHERE priority = 'low'");
            DB::statement("UPDATE tasks SET priority = 'moyenne' WHERE priority = 'medium'");
            DB::statement("UPDATE tasks SET priority = 'haute'   WHERE priority = 'high'");
            DB::statement("ALTER TABLE tasks MODIFY COLUMN priority ENUM('basse','moyenne','haute') DEFAULT 'basse'");
        }
    }

    public function down(): void
    {
        // Rien à faire
    }
};
