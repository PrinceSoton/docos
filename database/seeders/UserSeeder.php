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
            ['email' => 'info@nextmux.net'],
            [
                'nom'      => 'Admin',
                'prenom'   => 'Principal',
                'password' => Hash::make('225555555'),
                'role'     => 'admin',
                'actif'    => true,
            ]
        );
    }
}