<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {

            $table->id();

            // ── Informations du projet ──
            $table->string('title');           // (obligatoire)
            $table->text('description')->nullable(); // (optionnelle)

            // ── Clé étrangère : ce projet appartient à un utilisateur ──
            $table->foreignId('user_id')
                ->constrained()        // Fait référence à la table users
                ->onDelete('cascade'); // Si user supprimé → ses projets supprimés aussi

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects'); // Supprime la table si on rollback
    }
};
