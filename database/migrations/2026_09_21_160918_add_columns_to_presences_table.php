<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes heure_depart et marque_par à la table presences
     * sans nécessiter un migrate:fresh.
     *
     * Idempotente : chaque colonne n'est ajoutée que si elle n'existe pas
     *  ce qui évite tout conflit SQL.
     */
    public function up(): void
    {

        // Colonne heure_depart (time, nullable)// -----------------------------------------------------------------
        if (!Schema::hasColumn('presences', 'heure_depart')) {
            Schema::table('presences', function (Blueprint $table) {
                $table->time('heure_depart')
                      ->nullable()
                      ->after('heure_arrivee');
            });
        }

        // Colonne marque_par
        if (!Schema::hasColumn('presences', 'marque_par')) {
            Schema::table('presences', function (Blueprint $table) {
                $table->foreignId('marque_par')
                      ->nullable()
                      ->after('justificatif')
                      ->constrained('users')
                      ->nullOnDelete();
            });
        }
    }

    /**
     * Rollback : retire d'abord la contrainte FK, puis la colonne.
     */
    public function down(): void
    {
        //  Retrait de marque_par (FK + colonne)
        if (Schema::hasColumn('presences', 'marque_par')) {
            Schema::table('presences', function (Blueprint $table) {
                $table->dropForeign(['marque_par']);
            });

            Schema::table('presences', function (Blueprint $table) {
                $table->dropColumn('marque_par');
            });
        }

        //  Suppression de heure_depart
        if (Schema::hasColumn('presences', 'heure_depart')) {
            Schema::table('presences', function (Blueprint $table) {
                $table->dropColumn('heure_depart');
            });
        }
    }
};
