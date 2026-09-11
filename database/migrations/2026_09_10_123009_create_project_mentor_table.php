<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_mentor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['project_id', 'user_id']);
        });

        // Récupérer uniquement les projets qui ont un mentor non nul
        $projects = DB::table('projects')->whereNotNull('mentor_id')->get();
        
        $insertData = [];
        $timestamp = now();

        foreach ($projects as $project) {
            // Sécurité : Vérifier si l'utilisateur existe avant d'insérer
            $userExists = DB::table('users')->where('id', $project->mentor_id)->exists();
            
            if ($userExists) {
                $insertData[] = [
                    'project_id' => $project->id,
                    'user_id'    => $project->mentor_id,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        // Insertion groupée (plus rapide et performante)
        if (!empty($insertData)) {
            DB::table('project_mentor')->insert($insertData);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('project_mentor');
    }
};
