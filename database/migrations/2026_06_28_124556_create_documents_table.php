<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('fichier');
            $table->string('type_fichier')->nullable();
            $table->bigInteger('taille')->nullable();
            $table->boolean('partage_tous')->default(false);
            $table->timestamps();
        });

        // Table pivot document <-> utilisateurs partagés
        Schema::create('document_partage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_partage');
        Schema::dropIfExists('documents');
    }
};