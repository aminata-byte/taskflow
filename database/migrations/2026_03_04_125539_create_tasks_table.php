<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {

            $table->id(); // Clé primaire auto-incrémentée

            // ── Contenu de la tâche ──
            $table->string('title');                 // Titre de la tâche (obligatoire)
            $table->text('description')->nullable(); // Description (optionnelle)

            // ── Date limite (peut être vide) ──
            $table->date('due_date')->nullable(); // Date limite de la tâche

            // ── Niveau de priorité ──
            $table->enum('priority', ['basse', 'moyenne', 'haute'])
                ->default('basse');

            // ── Clé étrangère : cette tâche appartient à une colonne ──
            $table->foreignId('column_id')
                ->constrained('columns') // Fait référence à la table columns
                ->onDelete('cascade');   // Si colonne supprimée → ses tâches supprimées aussi

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks'); // Supprime la table si on rollback
    }
};
