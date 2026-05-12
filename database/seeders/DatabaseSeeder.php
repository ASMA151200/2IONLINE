<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@2ionline.com',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Formateur Test',
            'email' => 'formateur@2ionline.com',
            'password' => 'formateur123',
            'role' => 'formateur',
        ]);

        User::create([
            'name' => 'Etudiant Test',
            'email' => 'etudiant@2ionline.com',
            'password' => 'etudiant123',
            'role' => 'etudiant',
        ]);
    }
}
