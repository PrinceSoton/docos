<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->cascadeOnDelete();
            $table->date('date');
            $table->enum('statut', ['present', 'retard', 'absent'])->default('present');
            $table->string('motif')->nullable();
            $table->string('justificatif')->nullable();
            $table->time('heure_arrivee')->nullable();
            $table->timestamps();
            $table->unique(['stagiaire_id', 'date']);
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->cascadeOnDelete();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->text('motif');
            $table->string('justificatif')->nullable();
            $table->enum('statut', ['en_attente', 'valide', 'refuse'])->default('en_attente');
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->text('commentaire_mentor')->nullable();
            $table->timestamp('demande_le')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('presences');
    }
};