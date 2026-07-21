<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cree_par')->constrained('users')->cascadeOnDelete();
            $table->string('titre');
            $table->text('contenu')->nullable();
            $table->string('image')->nullable();
            $table->string('fichier')->nullable();
            $table->enum('type', ['information', 'evenement', 'note'])->default('information');
            $table->boolean('partage_tous')->default(true);
            $table->datetime('date_evenement')->nullable();
            $table->timestamps();
        });

        // Table pivot evenement <-> utilisateurs ciblés
        Schema::create('evenement_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evenement_id')->constrained('evenements')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evenement_user');
        Schema::dropIfExists('evenements');
    }
};