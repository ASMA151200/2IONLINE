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

    // Liste formateurs — inclut les formations dont ce formateur est
    // réellement propriétaire (formations.user_id), pas juste les
    // modules, pour que l'admin voie/gère cette assignation.
    public function getAll()
    {
        return Formateur::with(['user', 'modules'])
            ->latest()
            ->get()
            ->each(fn ($f) => $f->setRelation('formations', Formation::where('user_id', $f->user_id)->get(['id', 'titre'])));
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

            // Assignation de formation : ATTENTION, c'est CE champ
            // (formations.user_id) qui détermine réellement l'accès du
            // formateur au contenu (voir ChecksFormationOwnership) — pas
            // les modules, qui ne sont qu'informatifs. Sans cette
            // assignation, un formateur nouvellement créé ne peut gérer
            // AUCUN contenu, même avec des modules sélectionnés.
            if (!empty($data['formation_id'])) {
                Formation::where('id', $data['formation_id'])->update(['user_id' => $user->id]);
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
    public function getById(int $id):Formateur
    {
        $formateur = Formateur::with(['user', 'modules'])->findOrFail($id);
        $formateur->setRelation('formations', Formation::where('user_id', $formateur->user_id)->get(['id', 'titre']));
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

        // Réassignation de formation (même logique qu'à la création) —
        // isset() et pas empty() : transmettre formation_id: null doit
        // pouvoir retirer l'assignation actuelle si l'admin le souhaite.
        if (array_key_exists('formation_id', $data)) {
            // Retire d'abord ce formateur de toute formation qu'il
            // possédait déjà (au cas où il changerait d'assignation),
            // avant d'assigner la nouvelle.
            Formation::where('user_id', $formateur->user_id)->update(['user_id' => null]);

            if (!empty($data['formation_id'])) {
                Formation::where('id', $data['formation_id'])->update(['user_id' => $formateur->user_id]);
            }
        }

        return $formateur->load(['user','modules']);


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
