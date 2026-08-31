<?php

namespace Database\Seeders;

use App\Models\Actus;
use Illuminate\Database\Seeder;

class ActusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Actus::query()->delete();

        $actus = [
            [
                'titre' => 'Ouverture des inscriptions pour la session 2026',
                'description' => "Les inscriptions pour la prochaine session de formations CAP Hôtellerie-Restauration sont désormais ouvertes.",
                'contenu_html' => "<p>2I Online et Incub Institut ont le plaisir d'annoncer l'ouverture des inscriptions pour la session 2026 de nos formations certifiantes en Cuisine, Pâtisserie et Service.</p><p>Les places étant limitées, nous encourageons les candidats à s'inscrire dès maintenant via la plateforme. Le paiement peut se faire en ligne (Wave, Orange Money, Free Money) ou directement à notre centre de Bargny.</p>",
                'image' => null,
                'type' => 'actualite',
                'date_publication' => now()->subDays(10)->toDateTimeString(),
                'date_expiration' => now()->addMonths(3)->toDateTimeString(),
                'statut' => 'publie',
            ],
            [
                'titre' => 'Nos étudiants brillent au concours régional de pâtisserie',
                'description' => "Trois étudiantes du CAP Pâtissier ont représenté l'école lors du concours régional de Dakar.",
                'contenu_html' => "<p>Nous sommes fiers d'annoncer que trois de nos étudiantes en Certificat Professionnel de Spécialité-Pâtissier ont remporté le 2ème prix du concours régional de pâtisserie de Dakar, face à des candidats venus de toute la sous-région.</p><p>Cette performance illustre la qualité de la formation pratique dispensée dans nos ateliers, encadrés par des chefs formateurs expérimentés.</p>",
                'image' => null,
                'type' => 'evenement',
                'date_publication' => now()->subDays(20)->toDateTimeString(),
                'date_expiration' => now()->addMonths(2)->toDateTimeString(),
                'statut' => 'publie',
            ],
            [
                'titre' => 'Nouveau partenariat avec KaNora Services',
                'description' => "2I Online renforce son engagement social avec un nouveau partenaire dédié à l'inclusion.",
                'contenu_html' => "<p>Nous sommes heureux d'annoncer notre nouveau partenariat avec <strong>KaNora Services</strong>, acteur engagé dans l'inclusion sociale au Sénégal.</p><p>Ce partenariat permettra de financer des bourses complètes pour des jeunes issus de quartiers défavorisés, leur donnant accès à nos formations certifiantes en hôtellerie-restauration.</p>",
                'image' => null,
                'type' => 'communique',
                'date_publication' => now()->subDays(35)->toDateTimeString(),
                'date_expiration' => now()->addMonths(6)->toDateTimeString(),
                'statut' => 'publie',
            ],
            [
                'titre' => 'Portes ouvertes au centre de formation de Bargny',
                'description' => "Venez découvrir nos ateliers de cuisine et de pâtisserie lors de notre journée portes ouvertes.",
                'contenu_html' => "<p>Le centre Incub Institut de Bargny ouvrira ses portes au public le temps d'une journée pour présenter ses formations, ses équipements et rencontrer l'équipe pédagogique.</p><p>Au programme : démonstrations culinaires, dégustations préparées par nos étudiants, et séances d'information sur les modalités d'inscription et de financement.</p>",
                'image' => null,
                'type' => 'evenement',
                'date_publication' => now()->subDays(5)->toDateTimeString(),
                'date_expiration' => now()->addMonth()->toDateTimeString(),
                'statut' => 'publie',
            ],
            [
                'titre' => 'Lancement de la formation Incubation Street Food',
                'description' => "Un nouveau programme dédié aux entrepreneurs de la restauration de rue.",
                'contenu_html' => "<p>Face à la demande croissante d'accompagnement pour les jeunes entrepreneurs du secteur informel, 2I Online lance officiellement son programme <strong>Incubation Street Food</strong>.</p><p>Ce parcours de 3 mois combine formation culinaire, gestion de petite entreprise et accompagnement personnalisé pour aider les porteurs de projet à structurer et lancer leur activité de restauration ambulante.</p>",
                'image' => null,
                'type' => 'actualite',
                'date_publication' => now()->subDays(50)->toDateTimeString(),
                'date_expiration' => now()->addMonths(4)->toDateTimeString(),
                'statut' => 'publie',
            ],
            [
                'titre' => "Cérémonie de remise des certificats — Promotion 2025",
                'description' => "Retour en images sur la cérémonie qui a récompensé nos diplômés de l'année.",
                'contenu_html' => "<p>C'est avec beaucoup d'émotion que nous avons remis leurs certificats aux étudiants de la promotion 2025, en présence de leurs familles et de nos partenaires employeurs.</p><p>Un grand bravo à tous nos diplômés pour leur travail et leur persévérance tout au long de leur parcours de formation !</p>",
                'image' => null,
                'type' => 'evenement',
                'date_publication' => now()->subDays(60)->toDateTimeString(),
                'date_expiration' => now()->addMonths(2)->toDateTimeString(),
                'statut' => 'publie',
            ],
        ];

        foreach ($actus as $item) {
            Actus::create($item);
        }

        $this->command->info(count($actus) . ' actualités créées avec succès.');
    }
}
