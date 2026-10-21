<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Supprimer la contrainte FK existante
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        // 2) Modifier la colonne pour la rendre nullable
        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->change();
        });

        // 3) Recréer la contrainte FK (nullable + nullOnDelete pour conserver les tâches
        //    indépendantes si le projet est supprimé)
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Supprimer d'abord les tâches indépendantes (sinon la contrainte NOT NULL échoue)
        DB::table('tasks')->whereNull('project_id')->delete();

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->cascadeOnDelete();
        });
    }
};