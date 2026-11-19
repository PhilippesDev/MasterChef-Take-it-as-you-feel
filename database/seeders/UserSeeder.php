<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Comptes de demonstration pour les quatre roles du projet.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Administrateur Masterchef',
                'email' => 'admin@masterchef.test',
                'phone' => '+243 970 000 001',
                'role' => UserRole::Admin,
                'address' => 'Goma, Nord-Kivu',
            ],
            [
                'name' => 'Chef Cuisine',
                'email' => 'chef@masterchef.test',
                'phone' => '+243 970 000 002',
                'role' => UserRole::Chef,
                'address' => 'Goma, Nord-Kivu',
            ],
            [
                'name' => 'Livreur Goma',
                'email' => 'livreur@masterchef.test',
                'phone' => '+243 970 000 003',
                'role' => UserRole::Livreur,
                'address' => 'Goma, Nord-Kivu',
            ],
            [
                'name' => 'Client Test',
                'email' => 'client@masterchef.test',
                'phone' => '+243 970 000 004',
                'role' => UserRole::Client,
                'address' => 'Avenue du Lac 12, Goma',
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    ...$account,
                    // Le cast "hashed" du modele chiffre cette valeur.
                    'password' => 'password',
                ]
            );
        }
    }
}
