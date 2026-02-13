<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Création de l'ADMIN
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('Admin1234!'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Création du GESTIONNAIRE
        User::updateOrCreate(
            ['email' => 'gestion@gmail.com'],
            [
                'name' => 'Gestionnaire Stock',
                'password' => Hash::make('Gestion1234!'),
                'role' => 'manager', // ou 'gestionnaire' selon ton code
                'email_verified_at' => now(),
            ]
        );
    }
}