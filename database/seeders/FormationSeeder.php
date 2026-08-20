<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Formation;
use App\Models\User;

class FormationSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. Catégorie par défaut ---
        $categorieId = DB::table('categories')->where('titre', 'Hôtellerie & Restauration')->value('id');

        if (!$categorieId) {
            $categorieId = DB::table('categories')->insertGetId([
                'titre'      => 'Hôtellerie & Restauration', //  'titre' au lieu de 'nom'
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->command->info("Catégorie 'Hôtellerie & Restauration' créée (id={$categorieId}).");
        }

        // --- 2. Propriétaire ---
        $owner = User::whereIn('role', ['admin', 'formateur'])->first();

        if (!$owner) {
            $this->command->error(
                "Aucun utilisateur avec le rôle 'admin' ou 'formateur' trouvé."
            );
            return;
        }

        // --- 3. Formations ---
        $formations = [
    [
        'titre'       => 'CAP Cuisinier',
        'description' => 'Le CAP Cuisinier est une formation complète qui vous prépare au métier de cuisinier professionnel. Sur 36 mois, vous apprenez l\'ensemble des techniques culinaires.',
        'image'       => '/images/course-cuisine.jpg',
        'niveau'      => 'Débutant',
        'duree'       => '3 ans / 36 mois',
        'prix'        => 60000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
    [
        'titre'       => 'CAP Pâtissier',
        'description' => 'Le CAP Pâtisserie vous forme aux techniques essentielles de l\'art de la pâtisserie sur 36 mois.',
        'image'       => '/images/course-patisserie.jpg',
        'niveau'      => 'Débutant',
        'duree'       => '3 ans / 36 mois',
        'prix'        => 60000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
    [
        'titre'       => 'CAP Serveur',
        'description' => 'Le CAP Serveur vous prépare au métier de serveur en hôtellerie sur 36 mois.',
        'image'       => '/images/course-service.jpg',
        'niveau'      => 'Débutant',
        'duree'       => '3 ans / 36 mois',
        'prix'        => 60000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
    [
        'titre'       => 'VAE',
        'description' => 'La VAE permet de transformer votre expérience professionnelle en diplôme reconnu en 4 à 6 mois.',
        'image'       => '/images/VAE.jpg',
        'niveau'      => 'Tous niveaux',
        'duree'       => '4 à 6 mois',
        'prix'        => 150000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
    [
        'titre'       => 'Certificat Professionnel de Spécialité-Cuisinier',
        'description' => 'Formation courte et intensive de 6 mois pour approfondir une spécialité culinaire.',
        'image'       => '/images/course-cuisine1.jpg',
        'niveau'      => 'Intermédiaire',
        'duree'       => '6 mois',
        'prix'        => 60000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
    [
        'titre'       => 'Certificat Professionnel de Spécialité-Pâtissier',
        'description' => 'Formation de 6 mois pour approfondir votre maîtrise de la pâtisserie.',
        'image'       => '/images/course-patisserie1.jpg',
        'niveau'      => 'Intermédiaire',
        'duree'       => '6 mois',
        'prix'        => 60000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
    [
        'titre'       => 'Certificat Professionnel de Spécialité-Serveur',
        'description' => 'Formation de 6 mois axée sur l\'excellence du service en restauration gastronomique.',
        'image'       => '/images/course-service1.jpg',
        'niveau'      => 'Intermédiaire',
        'duree'       => '6 mois',
        'prix'        => 60000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
    [
        'titre'       => 'Travail à domicile',
        'description' => 'Programme d\'un mois pour accompagner les travailleurs domestiques dans le développement de leurs compétences.',
        'image'       => '/images/travail-domicile.jpg',
        'niveau'      => 'Débutant',
        'duree'       => '1 mois',
        'prix'        => 60000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
    [
        'titre'       => 'HACCP',
        'description' => 'Formation aux normes d\'hygiène et de sécurité alimentaire en 2 mois.',
        'image'       => '/images/course-haccp.jpg',
        'niveau'      => 'Tous niveaux',
        'duree'       => '2 mois',
        'prix'        => 100000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
    [
        'titre'       => 'INCUBATION STREET FOOD',
        'description' => 'Programme de 3 mois destiné aux jeunes et aux femmes porteurs de projet dans le secteur de l\'alimentation de rue.',
        'image'       => '/images/incubation-food.jpg',
        'niveau'      => 'Tous niveaux',
        'duree'       => '3 mois',
        'prix'        => 100000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
    [
        'titre'       => 'Gestion de restauration',
        'description' => 'Formation de 2 mois pour gérer un établissement performant.',
        'image'       => '/images/course-management.jpg',
        'niveau'      => 'Intermédiaire',
        'duree'       => '2 mois',
        'prix'        => 100000.00,
        'statut'      => 'hybride',
        'nb_inscrit'  => 0,
    ],
];

        foreach ($formations as $data) {
            Formation::updateOrCreate(
                ['titre' => $data['titre']],
                array_merge($data, [
                    'categorie_id' => $categorieId,
                    'user_id'      => $owner->id,
                ])
            );
        }

        $this->command->info(count($formations) . " formations seedées avec succès (propriétaire: {$owner->email}).");
    }
}
