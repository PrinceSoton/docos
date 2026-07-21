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
            $table->string('titre');
            $table->text('description')->nullable();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'termine', 'suspendu'])->default('en_attente');
            $table->enum('priorite', ['faible', 'normale', 'haute', 'urgente'])->default('normale');
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Table pivot entre projet et stagiaire
        Schema::create('project_stagiaire', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_stagiaire');
        Schema::dropIfExists('projects');
    }
};
