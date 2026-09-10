<?php

namespace App\Services;

use App\Models\User;
use App\Models\Formateur;
use App\Models\Formation;
use App\Enums\UserRole;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Mail\FormateurCreeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class FormateurService
{

    // Liste formateurs — inclut les formations dans lesquelles ce
    // formateur est autorisé à intervenir (table pivot
    // formation_formateur, plusieurs-à-plusieurs), pas juste les
    // modules, pour que l'admin voie/gère ces accès.
    public function getAll()
    {
        return Formateur::with(['user', 'modules', 'user.formationsEnseignees'])->latest()->get();
    }


    //Création formateur
    public function create(array $data): array
    {
        return DB::transaction(function () use ($data) {

            $password = Str::random(6);

            $user = User::create([
                'prenom'    => $data['prenom'],
                'nom'       => $data['nom'],
                'telephone' => $data['telephone'],
                'email'     => $data['email'],
                'password'  => Hash::make($password),
                'role'      => UserRole::formateur->value,
            ]);

            $formateur = $user->formateur()->create([
                'specialite' => $data['specialite'],
            ]);

            if (!empty($data['modules'])) {
                $formateur->modules()->sync($data['modules']);
            }

            // Assignation de formation(s) : ATTENTION, c'est CETTE
            // relation (table pivot formation_formateur) qui détermine
            // réellement l'accès du formateur au contenu (voir
            // ChecksFormationOwnership) — pas les modules, qui ne sont
            // qu'informatifs. Sans assignation, un formateur nouvellement
            // créé ne peut gérer AUCUN contenu, même avec des modules
            // sélectionnés. Un formateur peut désormais intervenir dans
            // PLUSIEURS formations — accepte un tableau formation_ids.
            if (!empty($data['formation_ids'])) {
                $user->formationsEnseignees()->sync($data['formation_ids']);
            }

            // Recharger les modules avant d'envoyer le mail
            $formateur->load(['user', 'modules']);

            // Envoi du mail
            Mail::to($user->email)->send(new FormateurCreeMail($formateur, $password));

            return [
                'formateur' => $formateur,
                'password'  => $password,
            ];
        });
    }


    //Afficher un formateur
    public function getById(int $id): Formateur
    {
        $formateur = Formateur::with(['user', 'modules', 'user.formationsEnseignees'])->findOrFail($id);
        return $formateur;
    }


    //Modifier un formateur
    public function update(Formateur $formateur, array $data)
    {
        $formateur->user->update([

        'prenom' => $data['prenom'] ?? $formateur->user->prenom,

        'nom' => $data['nom']?? $formateur->user->nom,

        'telephone' => $data['telephone'] ?? $formateur->user->telephone,

        'email' => $data['email']?? $formateur->user->email
        ]);


        //Mise à jour profil formateur
        $formateur->update([

            'specialite' => $data['specialite']?? $formateur->specialite,

        ]);

        //Mise à jour modules

        if (isset($data['modules'])) {

            $formateur->modules()->sync($data['modules']);
        }

        // Réassignation de formation(s) — isset() et pas empty() : un
        // tableau vide formation_ids: [] doit pouvoir retirer TOUTES
        // les assignations actuelles si l'admin le souhaite. sync()
        // gère lui-même l'ajout ET le retrait en une seule opération,
        // contrairement à l'ancien modèle à formation unique qui devait
        // explicitement "libérer" l'ancienne avant d'assigner la
        // nouvelle.
        if (array_key_exists('formation_ids', $data)) {
            $formateur->user->formationsEnseignees()->sync($data['formation_ids'] ?? []);
        }

        return $formateur->load(['user', 'modules', 'user.formationsEnseignees']);
    }

    //Supprimer
    public function destroy(Formateur $formateur): void {

        $formateur->deleteOrFail();
    }

    // Activer / désactiver le compte
    public function toggleActive(Formateur $formateur): User
    {
        $user = $formateur->user;
        $user->update(['is_active' => !$user->is_active]);
        return $user;
    }

    // Réinitialiser le mot de passe (génère et envoie par email)
    public function resetPassword(Formateur $formateur): string
    {
        $password = Str::random(6);
        $formateur->user->update(['password' => Hash::make($password)]);

        Mail::to($formateur->user->email)->send(new FormateurCreeMail($formateur->load('user', 'modules'), $password));

        return $password;
    }
}
