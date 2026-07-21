<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('fichier');
            $table->enum('type', ['journalier', 'hebdomadaire', 'mensuel', 'final', 'autre'])->default('autre');
            $table->enum('statut', ['soumis', 'valide', 'rejete', 'en_revision'])->default('soumis');
            $table->integer('note')->nullable();
            $table->text('commentaire_mentor')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};