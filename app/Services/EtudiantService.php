<?php

namespace App\Services;

use App\Models\User;
use App\Models\Etudiant;
use App\Enums\UserRole;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Mail\EtudiantCreeMail;
use Illuminate\Support\Facades\Mail;
use App\Support\SafeMail;
use Illuminate\Support\Facades\DB;

class EtudiantService
{
    //Liste des etudiants
    public function getAll()
    {
        return Etudiant::with(['user', 'formations'])->get();
    }

    //creer un etudiant
    public function store(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $password = Str::random(6);

            $user = User::create([
                'prenom'    => $data['prenom'],
                'nom'       => $data['nom'],
                'telephone' => $data['telephone'],
                'email'     => $data['email'],
                'password'  => Hash::make($password),
                'role'      => UserRole::etudiant->value,
            ]);

            $etudiant = $user->etudiant()->create([
                'date_naissance' => $data['date_naissance'] ?? null,
                'lieu_naissance' => $data['lieu_naissance'] ?? null,
                'niveau'         => $data['niveau'] ?? null,
            ]);

            if (!empty($data['formations'])) {
                $etudiant->formations()->sync($data['formations']);

                // IMPORTANT: le lien ci-dessus (etudiant_formation) est
                // purement informatif — ce n'est PAS ce qui donne
                // réellement accès au contenu des leçons. Seule une
                // inscription active (table inscriptions, statut='actif')
                // le permet (voir LeconController::authorizeFormationAccess).
                // Normalement créée par le webhook PayDunya après un vrai
                // paiement en ligne ; ici on la crée directement pour
                // couvrir le scénario "paiement en liquide hors
                // plateforme" — l'étudiant ajouté par l'admin doit
                // pouvoir se connecter et voir ses leçons immédiatement,
                // sans jamais passer par PayDunya.
                foreach ($data['formations'] as $formationId) {
                    \App\Models\Inscription::updateOrCreate(
                        ['user_id' => $user->id, 'formation_id' => $formationId],
                        ['date' => now()->toDateString(), 'statut' => 'actif'],
                    );
                }
            }

            // Recharger les formations avant d'envoyer le mail
            $etudiant->load(['formations']);

            // Envoi du mail
            SafeMail::queue($user->email, new EtudiantCreeMail($etudiant, $password));

            return [
                'etudiant' => $etudiant->load(['user', 'formations']),
                'password'  => $password,
            ];
        });
    }

    // Afficher un etudiant
    public function show(Etudiant $etudiant): Etudiant
    {
        return $etudiant->load(['user', 'formations']);
    }

    // update etudiant
    public function update(Etudiant $etudiant, array $data): Etudiant
    {
        $etudiant->user->update([
            'prenom'    => $data['prenom'] ?? $etudiant->user->prenom,
            'nom'       => $data['nom'] ?? $etudiant->user->nom,
            'telephone' => $data['telephone'] ?? $etudiant->user->telephone,
            'email'     => $data['email'] ?? $etudiant->user->email,
        ]);

        $etudiant->update([
            'date_naissance' => $data['date_naissance'] ?? $etudiant->date_naissance,
            'lieu_naissance' => $data['lieu_naissance'] ?? $etudiant->lieu_naissance,
            'niveau'         => $data['niveau'] ?? $etudiant->niveau,
        ]);

        if (isset($data['formations'])) {
            $etudiant->formations()->sync($data['formations']);

            // CHANGEMENT de formation : l'ancienne inscription active passe à
            // « annulé » (ligne conservée, paiements intacts). Avant, elle
            // restait active : l'apprenant gardait l'accès aux DEUX formations,
            // d'où les doubles suivis. Seulement si une nouvelle formation est
            // fournie : une liste vide ne touche à aucune inscription.
            if (!empty($data['formations'])) {
                \App\Models\Inscription::where('user_id', $etudiant->user_id)
                    ->where('statut', 'actif')
                    ->whereNotIn('formation_id', $data['formations'])
                    ->update(['statut' => 'annule']);
            }

            // Même logique qu'à la création : garantit un accès réel
            // (inscription active) pour la formation choisie.
            foreach ($data['formations'] as $formationId) {
                \App\Models\Inscription::updateOrCreate(
                    ['user_id' => $etudiant->user_id, 'formation_id' => $formationId],
                    ['date' => now()->toDateString(), 'statut' => 'actif'],
                );
            }
        }

        return $etudiant->load(['user', 'formations']);
    }


    //Supprimer un etudiant
    public function delete(Etudiant $etudiant): void
    {
        DB::transaction(function () use ($etudiant) {
            $etudiant->formations()->detach();
            $user = $etudiant->user;
            $etudiant->deleteOrFail();
            $user->deleteOrFail();
        });
    }

    // Activer / désactiver le compte
    public function toggleActive(Etudiant $etudiant): User
    {
        $user = $etudiant->user;
        $user->update(['is_active' => !$user->is_active]);
        return $user;
    }

    // Réinitialiser le mot de passe (génère et envoie par email)
    public function resetPassword(Etudiant $etudiant): string
    {
        $password = Str::random(6);
        $etudiant->user->update(['password' => Hash::make($password)]);

        SafeMail::queue($etudiant->user->email, new EtudiantCreeMail($etudiant->load('formations'), $password));

        return $password;
    }

    // Voir ses cours (étudiant connecté)
    public function voirCours(User $user)
    {
        $etudiant = $user->etudiant()->with([
            'formations.modules.lecons',
            'formations.categorie'
        ])->firstOrFail();

        return $etudiant->formations;
    }

}
