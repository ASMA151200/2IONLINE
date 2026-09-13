<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;

/**
 * Seed des catégories de formation — deux catégories seulement,
 * demandées explicitement : Hôtellerie et Restauration.
 *
 * Utilise firstOrCreate() sur "titre" (colonne unique) — rejouable sans
 * créer de doublons ni écraser une catégorie déjà en place.
 *
 * Exécution : php artisan db:seed --class=CategorieSeeder
 */
class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'titre' => 'Hôtellerie',
                'description' => "Formations aux métiers de l'hôtellerie et des services associés.",
            ],
            [
                'titre' => 'Restauration',
                'description' => "Formations aux métiers de la restauration : cuisine, pâtisserie, service en salle et gestion d'établissement.",
            ],
        ];

        foreach ($categories as $data) {
            Categorie::firstOrCreate(['titre' => $data['titre']], $data);
        }

        $this->command->info(count($categories) . ' catégories seedées avec succès.');
    }
}
