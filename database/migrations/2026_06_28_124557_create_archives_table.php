<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['stagiaire', 'autre'])->default('autre');
            $table->foreignId('stagiaire_id')->nullable()->constrained('stagiaires')->nullOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->foreignId('cree_par')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('archive_fichiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('archive_id')->constrained('archives')->cascadeOnDelete();
            $table->string('nom_original');
            $table->string('chemin');
            $table->string('type_fichier')->nullable();
            $table->bigInteger('taille')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archive_fichiers');
        Schema::dropIfExists('archives');
    }
};