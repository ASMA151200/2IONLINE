<?php

namespace App\Services;

use App\Models\User;
use App\Models\Formateur;
use App\Enums\UserRole;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Mail\FormateurCreeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class FormateurService
{

    // Liste formateurs
    public function getAll()
    {
        return Formateur::with(['user','modules'])->latest()->get();
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
        return Formateur::with(['user', 'modules'])->findOrFail($id);
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

        return $formateur->load(['user','modules']);


    }

    //Supprimer
    public function destroy(Formateur $formateur): void {

        $formateur->deleteOrFail();
    }
}
