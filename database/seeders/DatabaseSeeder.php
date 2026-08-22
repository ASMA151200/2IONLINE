<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ordre important : UserSeeder d'abord (crée le compte admin de
        // test nécessaire à FormationsSeeder pour désigner un propriétaire),
        // puis FormationsSeeder. Les autres seeders sont encore des
        // coquilles vides (ActusSeeder, ModuleSeeder, etc.) — à compléter
        // au fur et à mesure, ils ne font rien pour l'instant.
        $this->call([
            UserSeeder::class,
            FormationsSeeder::class,
        ]);
    }
}
