<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
    'prenom' => 'Test',
    'nom' => 'User',
    'telephone' => '770000000',
    'email' => 'test@example.com',
    'password' => Hash::make('password123'),
    'role' => 'admin',
]);
    }
}