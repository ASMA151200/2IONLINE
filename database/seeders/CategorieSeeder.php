<?php

namespace Database\Seeders;

use App\Models\Categorie;
use Illuminate\Database\Seeder;

/**
 * Seed des catégories de formation — jusqu'ici, une seule catégorie
 * générique ("Hôtellerie & Restauration") existait, créée à la volée
 * par FormationsSeeder et utilisée pour TOUTES les formations sans
 * distinction. Ce seeder introduit un vrai découpage par domaine,
 * cohérent avec le catalogue réel de formations (voir FormationsSeeder),
 * pour que la navigation/filtrage par catégorie ait un sens sur le site
 * public.
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
                'titre' => 'Cuisine',
                'description' => "Formations aux techniques culinaires, de l'initiation aux spécialités gastronomiques.",
            ],
            [
                'titre' => 'Pâtisserie',
                'description' => "Formations dédiées à l'art de la pâtisserie, de la boulangerie aux desserts élaborés.",
            ],
            [
                'titre' => 'Service & Restauration',
                'description' => "Formations au service en salle, à l'accueil client et à l'excellence du service en restauration.",
            ],
            [
                'titre' => 'Gestion & Management',
                'description' => "Formations à la gestion d'établissement, au pilotage financier et au management d'équipe en restauration.",
            ],
            [
                'titre' => 'Hygiène & Sécurité Alimentaire',
                'description' => "Formations aux normes d'hygiène et de sécurité alimentaire (HACCP) obligatoires en restauration.",
            ],
            [
                'titre' => 'Entrepreneuriat & Incubation',
                'description' => "Programmes d'accompagnement à la création de projet dans l'alimentation et la restauration.",
            ],
            [
                'titre' => 'Validation des Acquis',
                'description' => "Accompagnement à la validation des acquis de l'expérience (VAE) pour transformer l'expérience professionnelle en diplôme.",
            ],
            [
                'titre' => 'Services à la Personne',
                'description' => "Formations aux métiers d'aide et de services à domicile.",
            ],
        ];

        foreach ($categories as $data) {
            Categorie::firstOrCreate(['titre' => $data['titre']], $data);
        }

        $this->command->info(count($categories) . ' catégories seedées avec succès.');
    }
}
