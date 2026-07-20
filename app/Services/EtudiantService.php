<?php

namespace App\Services;

use App\Models\User;
use App\Models\Etudiant;
use App\Enums\UserRole;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Mail\EtudiantCreeMail;
use Illuminate\Support\Facades\Mail;
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
            }

            // Recharger les formations avant d'envoyer le mail
            $etudiant->load(['formations']);

            // Envoi du mail
            Mail::to($user->email)->send(new EtudiantCreeMail($etudiant, $password));

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
    public function update(etudiant $etudiant, array $data): etudiant
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
