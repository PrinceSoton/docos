<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stagiaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('matricule')->unique();
            $table->string('ecole')->nullable();
            $table->string('niveau_etude')->nullable();
            $table->string('specialite')->nullable();
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('cv')->nullable();
            $table->text('description')->nullable();
            $table->enum('statut', ['en_cours', 'termine', 'suspendu'])->default('en_cours');
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stagiaires');
    }
};
