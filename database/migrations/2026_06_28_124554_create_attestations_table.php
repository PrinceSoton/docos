<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stagiaire_id')->constrained('stagiaires')->cascadeOnDelete();
            $table->enum('type', ['attestation', 'convention'])->default('attestation');
            $table->enum('statut', ['en_attente', 'valide_mentor', 'approuve_admin', 'envoye', 'refuse'])->default('en_attente');
            $table->text('motif_demande')->nullable();
            $table->string('fichier')->nullable();
            $table->foreignId('valide_par_mentor')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le_mentor')->nullable();
            $table->foreignId('envoye_par_admin')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('envoye_le')->nullable();
            $table->text('commentaire')->nullable();
            $table->boolean('demande_effectuee')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attestations');
    }
};