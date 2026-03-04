<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('columns', function (Blueprint $table) {

            $table->id(); // Clé primaire auto-incrémentée

            // ── Informations de la colonne ──
            $table->string('name');          // Nom : "À faire", "En cours", "Terminé"
            $table->integer('order')->default(0); // Ordre d'affichage dans le kanban

            // ── Clé étrangère : cette colonne appartient à un projet ──
            $table->foreignId('project_id')
                ->constrained()        // Fait référence à la table projects
                ->onDelete('cascade'); // Si projet supprimé → ses colonnes supprimées aussi

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('columns'); // Supprime la table si on rollback
    }
};
