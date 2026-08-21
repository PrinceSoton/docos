<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matricule_sequences', function (Blueprint $table) {
            $table->year('annee')->primary();
            $table->integer('dernier_numero')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matricule_sequences');
    }
};