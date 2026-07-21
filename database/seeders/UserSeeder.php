<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Mentor;
use App\Models\Stagiaire;
use App\Models\ConfigJoursTravail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'nom'      => 'Admin',
            'prenom'   => 'Principal',
            'email'    => 'admin@docos.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'actif'    => true,
        ]);

        // Mentor
        $mentorUser = User::create([
            'nom'      => 'Dupont',
            'prenom'   => 'Jean',
            'email'    => 'mentor@docos.com',
            'password' => Hash::make('password'),
            'role'     => 'mentor',
            'actif'    => true,
        ]);
        Mentor::create([
            'user_id'     => $mentorUser->id,
            'departement' => 'Informatique',
            'poste'       => 'Chef de projet',
        ]);

        // Stagiaire
        $stagUser = User::create([
            'nom'      => 'Martin',
            'prenom'   => 'Alice',
            'email'    => 'stagiaire@docos.com',
            'password' => Hash::make('password'),
            'role'     => 'stagiaire',
            'actif'    => true,
        ]);
        Stagiaire::create([
            'user_id'    => $stagUser->id,
            'matricule'  => Stagiaire::genererMatricule(),
            'ecole'      => 'École Supérieure',
            'specialite' => 'Génie Logiciel',
            'date_debut' => now()->startOfMonth(),
            'date_fin'   => now()->addMonths(6),
            'mentor_id'  => $mentorUser->id,
            'statut'     => 'en_cours',
        ]);

        // Configuration jours de travail par défaut
        ConfigJoursTravail::create([
            'lundi'    => true,
            'mardi'    => true,
            'mercredi' => true,
            'jeudi'    => true,
            'vendredi' => true,
            'samedi'   => false,
            'dimanche' => false,
            'heure_debut' => '09:00:00',
            'heure_fin'   => '18:15:00',
        ]);
    }
}