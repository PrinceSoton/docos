<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jours spéciaux (fériés, séjours)
        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('libelle');
            $table->enum('type', ['ferie', 'sejour', 'autre'])->default('ferie');
            $table->text('description')->nullable();
            $table->foreignId('cree_par')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique('date');
        });

        // Configuration des jours de travail
        Schema::create('config_jours_travail', function (Blueprint $table) {
            $table->id();
            $table->boolean('lundi')->default(true);
            $table->boolean('mardi')->default(true);
            $table->boolean('mercredi')->default(true);
            $table->boolean('jeudi')->default(true);
            $table->boolean('vendredi')->default(true);
            $table->boolean('samedi')->default(false);
            $table->boolean('dimanche')->default(false);
            $table->time('heure_debut')->default('09:00:00');
            $table->time('heure_fin')->default('18:15:00');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config_jours_travail');
        Schema::dropIfExists('calendars');
    }
};
