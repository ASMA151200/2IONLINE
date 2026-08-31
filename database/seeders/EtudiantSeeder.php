<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Formation;
use App\Models\Inscription;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Comptes étudiants de démonstration — mot de passe "password123" pour
 * tous. Chacun reçoit une inscription réellement active (pas juste le
 * lien informatif etudiant_formation) pour avoir un accès de test
 * complet aux leçons/forum/sondages de sa formation.
 */
class EtudiantSeeder extends Seeder
{
    public function run(): void
    {
        $formations = Formation::all()->keyBy('titre');

        if ($formations->isEmpty()) {
            $this->command->error('Aucune formation trouvée — lancez FormationsSeeder avant EtudiantSeeder.');
            return;
        }

        $etudiants = [
            [
                'prenom' => 'Awa', 'nom' => 'Fall', 'email' => 'awa.fall@example.com', 'telephone' => '771001111',
                'date_naissance' => '2002-03-14', 'lieu_naissance' => 'Dakar', 'niveau' => 'Débutant',
                'formation' => 'CAP Cuisinier',
            ],
            [
                'prenom' => 'Ibrahima', 'nom' => 'Sow', 'email' => 'ibrahima.sow@example.com', 'telephone' => '771002222',
                'date_naissance' => '2001-07-22', 'lieu_naissance' => 'Thiès', 'niveau' => 'Débutant',
                'formation' => 'CAP Pâtissier',
            ],
            [
                'prenom' => 'Mariama', 'nom' => 'Ba', 'email' => 'mariama.ba@example.com', 'telephone' => '771003333',
                'date_naissance' => '2003-01-09', 'lieu_naissance' => 'Bargny', 'niveau' => 'Débutant',
                'formation' => 'CAP Serveur',
            ],
            [
                'prenom' => 'Ousmane', 'nom' => 'Diouf', 'email' => 'ousmane.diouf@example.com', 'telephone' => '771004444',
                'date_naissance' => '2000-11-30', 'lieu_naissance' => 'Rufisque', 'niveau' => 'Intermédiaire',
                'formation' => 'Certificat Professionnel de Spécialité-Cuisinier',
            ],
            [
                'prenom' => 'Bineta', 'nom' => 'Camara', 'email' => 'bineta.camara@example.com', 'telephone' => '771005555',
                'date_naissance' => '2002-09-18', 'lieu_naissance' => 'Dakar', 'niveau' => 'Débutant',
                // Alumni visible dans l'annuaire public — pour tester ce parcours aussi
                'formation' => 'CAP Cuisinier',
                'alumni_visible' => true,
                'poste_actuel' => 'Commis de cuisine',
                'entreprise_actuelle' => 'Hôtel Terrou-Bi',
            ],
        ];

        foreach ($etudiants as $data) {
            $formation = $formations->get($data['formation']);

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'prenom' => $data['prenom'],
                    'nom' => $data['nom'],
                    'telephone' => $data['telephone'],
                    'password' => Hash::make('password123'),
                    'role' => UserRole::etudiant->value,
                    'is_active' => true,
                ],
            );

            $etudiant = $user->etudiant()->firstOrCreate([], [
                'date_naissance' => $data['date_naissance'],
                'lieu_naissance' => $data['lieu_naissance'],
                'niveau' => $data['niveau'],
                'alumni_visible' => $data['alumni_visible'] ?? false,
                'poste_actuel' => $data['poste_actuel'] ?? null,
                'entreprise_actuelle' => $data['entreprise_actuelle'] ?? null,
            ]);

            if ($formation) {
                $etudiant->formations()->syncWithoutDetaching([$formation->id]);

                // Inscription RÉELLEMENT active — sans elle, l'étudiant de
                // démo n'aurait accès à rien (voir le correctif appliqué à
                // EtudiantService pour le même problème côté admin).
                Inscription::updateOrCreate(
                    ['user_id' => $user->id, 'formation_id' => $formation->id],
                    ['date' => now()->subWeeks(2)->toDateString(), 'statut' => 'actif'],
                );
            }
        }

        $this->command->info(count($etudiants) . ' étudiants de démonstration créés avec succès.');
    }
}
