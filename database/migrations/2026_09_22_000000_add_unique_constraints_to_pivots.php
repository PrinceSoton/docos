<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'project_stagiaire' => ['project_id', 'stagiaire_id'],
            'document_partage' => ['document_id', 'user_id'],
            'evenement_user' => ['evenement_id', 'user_id'],
        ] as $table => [$firstColumn, $secondColumn]) {
            $duplicateExists = DB::table($table)
                ->select($firstColumn, $secondColumn)
                ->groupBy($firstColumn, $secondColumn)
                ->havingRaw('COUNT(*) > 1')
                ->exists();

            if ($duplicateExists) {
                throw new RuntimeException("Doublons détectés dans {$table}; nettoyage requis avant migration.");
            }
        }

        Schema::table('project_stagiaire', function (Blueprint $table): void {
            $table->unique(['project_id', 'stagiaire_id'], 'project_stagiaire_unique');
        });

        Schema::table('document_partage', function (Blueprint $table): void {
            $table->unique(['document_id', 'user_id'], 'document_partage_unique');
        });

        Schema::table('evenement_user', function (Blueprint $table): void {
            $table->unique(['evenement_id', 'user_id'], 'evenement_user_unique');
        });
    }

    public function down(): void
    {
        Schema::table('project_stagiaire', function (Blueprint $table): void {
            $table->dropUnique('project_stagiaire_unique');
        });

        Schema::table('document_partage', function (Blueprint $table): void {
            $table->dropUnique('document_partage_unique');
        });

        Schema::table('evenement_user', function (Blueprint $table): void {
            $table->dropUnique('evenement_user_unique');
        });
    }
};
