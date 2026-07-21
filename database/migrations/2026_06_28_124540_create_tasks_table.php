<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->enum('statut', ['a_faire', 'en_cours', 'termine'])->default('a_faire');
            $table->enum('priorite', ['faible', 'normale', 'haute', 'urgente'])->default('normale');
            $table->enum('difficulte', ['facile', 'moyen', 'difficile'])->default('moyen');
            $table->date('date_echeance')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
