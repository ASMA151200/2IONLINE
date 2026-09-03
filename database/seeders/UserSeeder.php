<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // firstOrCreate (pas create) : relancer "php artisan db:seed" sur
        // une base déjà partiellement peuplée ne doit jamais planter sur
        // ce compte de test déjà existant.
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'prenom' => 'Test',
                'nom' => 'User',
                'telephone' => '770000000',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ],
        );
    }
}