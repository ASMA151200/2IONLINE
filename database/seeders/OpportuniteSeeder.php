<?php

namespace Database\Seeders;

use App\Models\Opportunite;
use Illuminate\Database\Seeder;

class OpportuniteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Opportunite::query()->delete();

        $opportunites = [
            [
                'titre' => 'Commis de cuisine — Hôtel Terrou-Bi',
                'type' => 'emploi',
                'description' => "L'Hôtel Terrou-Bi recrute un(e) commis de cuisine pour renforcer son équipe de restauration. Poste ouvert aux diplômés CAP Cuisinier ou Certificat Professionnel de Spécialité-Cuisinier. Une première expérience en restauration collective ou gastronomique est un plus.",
                'documents' => null,
                'date_debut' => now()->addWeeks(2)->toDateString(),
                'date_fin' => now()->addMonths(2)->toDateString(),
                'ville' => 'Dakar',
                'pays' => 'Sénégal',
                'entreprise' => 'Hôtel Terrou-Bi',
                'lien_inscription' => null,
                'statut' => 'ouvert',
            ],
            [
                'titre' => 'Stage pâtisserie — Boulangerie Jacqueline',
                'type' => 'stage',
                'description' => "Stage pratique de 3 mois au sein de la Boulangerie Jacqueline, idéal pour un(e) étudiant(e) en fin de formation CAP Pâtissier souhaitant consolider ses acquis en conditions réelles de production.",
                'documents' => null,
                'date_debut' => now()->addMonth()->toDateString(),
                'date_fin' => now()->addMonths(4)->toDateString(),
                'ville' => 'Dakar',
                'pays' => 'Sénégal',
                'entreprise' => 'Boulangerie Jacqueline',
                'lien_inscription' => null,
                'statut' => 'ouvert',
            ],
            [
                'titre' => 'Serveur(se) qualifié(e) — Restaurant Le Lagon',
                'type' => 'emploi',
                'description' => "Le restaurant Le Lagon recherche un(e) serveur(se) qualifié(e), titulaire d'un Certificat Professionnel de Spécialité-Serveur, pour son établissement en bord de mer. Anglais souhaité pour l'accueil d'une clientèle internationale.",
                'documents' => null,
                'date_debut' => now()->addWeeks(3)->toDateString(),
                'date_fin' => now()->addMonths(2)->toDateString(),
                'ville' => 'Dakar',
                'pays' => 'Sénégal',
                'entreprise' => 'Restaurant Le Lagon',
                'lien_inscription' => null,
                'statut' => 'ouvert',
            ],
            [
                'titre' => 'Bourse d\'excellence Incub Institut 2026',
                'type' => 'bourse',
                'description' => "Incub Institut offre 10 bourses d'excellence couvrant l'intégralité des frais de formation pour les candidats démontrant un potentiel exceptionnel et une situation sociale nécessitant un accompagnement financier. Dossier de motivation et entretien requis.",
                'documents' => null,
                'date_debut' => now()->toDateString(),
                'date_fin' => now()->addMonths(3)->toDateString(),
                'ville' => 'Bargny',
                'pays' => 'Sénégal',
                'entreprise' => 'Incub Institut',
                'lien_inscription' => null,
                'statut' => 'ouvert',
            ],
            [
                'titre' => 'Partenariat traiteur événementiel — Sénégal Events',
                'type' => 'partenariat',
                'description' => "Sénégal Events recherche des jeunes diplômés du programme Incubation Street Food pour intégrer son réseau de prestataires traiteurs lors d'événements privés et professionnels à Dakar et sa région.",
                'documents' => null,
                'date_debut' => now()->addWeeks(1)->toDateString(),
                'date_fin' => now()->addMonths(6)->toDateString(),
                'ville' => 'Dakar',
                'pays' => 'Sénégal',
                'entreprise' => 'Sénégal Events',
                'lien_inscription' => null,
                'statut' => 'ouvert',
            ],
            [
                'titre' => 'Formation continue HACCP avancée',
                'type' => 'formation',
                'description' => "Module de perfectionnement en normes d'hygiène et sécurité alimentaire (HACCP), ouvert aux anciens diplômés souhaitant renforcer leurs compétences pour évoluer vers des postes de gestion en restauration collective.",
                'documents' => null,
                'date_debut' => now()->addMonths(2)->toDateString(),
                'date_fin' => now()->addMonths(3)->toDateString(),
                'ville' => 'Bargny',
                'pays' => 'Sénégal',
                'entreprise' => 'Incub Institut',
                'lien_inscription' => null,
                'statut' => 'ouvert',
            ],
        ];

        foreach ($opportunites as $item) {
            Opportunite::create($item);
        }

        $this->command->info(count($opportunites) . ' opportunités créées avec succès.');
    }
}
