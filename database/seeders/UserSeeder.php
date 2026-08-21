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
        $admin = User::firstOrCreate(
            ['email' => 'admin@docos.com'],
            [
                'nom'      => 'Admin',
                'prenom'   => 'Principal',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'actif'    => true,
            ]
        );

        $mentorUser = User::firstOrCreate(
            ['email' => 'mentor@docos.com'],
            [
                'nom'      => 'Dupont',
                'prenom'   => 'Jean',
                'password' => Hash::make('password'),
                'role'     => 'mentor',
                'actif'    => true,
            ]
        );

        if (! $mentorUser->mentor()->exists()) {
            Mentor::create([
                'user_id'     => $mentorUser->id,
                'departement' => 'Informatique',
                'poste'       => 'Chef de projet',
            ]);
        }

        $stagUser = User::firstOrCreate(
            ['email' => 'stagiaire@docos.com'],
            [
                'nom'      => 'Martin',
                'prenom'   => 'Alice',
                'password' => Hash::make('password'),
                'role'     => 'stagiaire',
                'actif'    => true,
            ]
        );

        if (! $stagUser->stagiaire()->exists()) {
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
        }

        ConfigJoursTravail::firstOrCreate(
            ['id' => 1],
            [
                'lundi'      => true,
                'mardi'      => true,
                'mercredi'   => true,
                'jeudi'      => true,
                'vendredi'   => true,
                'samedi'     => false,
                'dimanche'   => false,
                'heure_debut' => '09:00:00',
                'heure_fin'   => '18:15:00',
            ]
        );
    }
}