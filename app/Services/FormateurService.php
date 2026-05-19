<?php

namespace App\Services;

use App\Models\User;
use App\Models\Formateur;
use App\Models\Module;
use App\Enums\UserRole;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class FormateurService
{

    // Liste formateurs
    public function getAll()
    {
        return Formateur::with(['user','modules'])->latest()->get();
    }


    //Création formateur
    public function store(array $data)
    {
        //Création user

        $password = Str::random(6);

        $user = User::create([

            'prenom' => $data['prenom'],

            'nom' => $data['nom'],

            'telephone' => $data['telephone'],

            'email' =>$data['email'],

            'password' => Hash::make($password),

            'role' => UserRole::formateur->value
        ]);

        //Création profil formateur

        $formateur = Formateur::create([

            'user_id' => $user->id,

            'specialite' => $data['specialite']

        ]);


        //Assignation modules

        if (isset($data['modules'])) {

            $formateur->modules()->sync($data['modules']);
        }

        return [
            'formateur' => $formateur->load(['user','modules']),
            'password' => $password
        ];
    }


    //Afficher un formateur
    public function getById(int $id):Formateur
    {
        return Formateur::with(['user', 'modules'])->findOrFail($id);
    }


    //Mise a jour formateur
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
