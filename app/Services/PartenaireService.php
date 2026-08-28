<?php

namespace App\Services;

use App\Models\User;
use App\Models\Partenaire;
use App\Models\Formation;
use App\Enums\UserRole;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Mail\PartenaireCreeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class PartenaireService
{
    // Liste partenaires
    public function getAll()
    {
        return Partenaire::with(['user', 'formations'])->latest()->get();
    }

    // Création partenaire
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
                'role'      => UserRole::partenaire->value,
            ]);

            $partenaire = $user->partenaire()->create([
                'nom_organisation' => $data['nom_organisation'],
                'secteur' => $data['secteur'] ?? null,
            ]);

            $partenaire->load(['user', 'formations']);

            Mail::to($user->email)->send(new PartenaireCreeMail($partenaire, $password));

            return [
                'partenaire' => $partenaire,
                'password'   => $password,
            ];
        });
    }

    // Afficher un partenaire
    public function getById(int $id): Partenaire
    {
        return Partenaire::with(['user', 'formations'])->findOrFail($id);
    }

    // Modifier un partenaire
    public function update(Partenaire $partenaire, array $data): Partenaire
    {
        $partenaire->user->update([
            'prenom' => $data['prenom'] ?? $partenaire->user->prenom,
            'nom' => $data['nom'] ?? $partenaire->user->nom,
            'telephone' => $data['telephone'] ?? $partenaire->user->telephone,
            'email' => $data['email'] ?? $partenaire->user->email,
        ]);

        $partenaire->update([
            'nom_organisation' => $data['nom_organisation'] ?? $partenaire->nom_organisation,
            'secteur' => $data['secteur'] ?? $partenaire->secteur,
        ]);

        return $partenaire->load(['user', 'formations']);
    }

    // Supprimer
    public function destroy(Partenaire $partenaire): void
    {
        $partenaire->deleteOrFail();
    }

    // Activer / désactiver le compte
    public function toggleActive(Partenaire $partenaire): User
    {
        $user = $partenaire->user;
        $user->update(['is_active' => !$user->is_active]);
        return $user;
    }

    // Réinitialiser le mot de passe (génère et envoie par email)
    public function resetPassword(Partenaire $partenaire): string
    {
        $password = Str::random(6);
        $partenaire->user->update(['password' => Hash::make($password)]);

        Mail::to($partenaire->user->email)->send(new PartenaireCreeMail($partenaire->load('user', 'formations'), $password));

        return $password;
    }

    /**
     * Attache (ou met à jour) le financement d'une formation par ce
     * partenaire — montant + date. sync() avec un tableau associatif clé
     * = formation_id met à jour le pivot si déjà existant.
     */
    public function financerFormation(Partenaire $partenaire, int $formationId, float $montant, string $date): void
    {
        $partenaire->formations()->syncWithoutDetaching([
            $formationId => ['montant_finance' => $montant, 'date_financement' => $date],
        ]);
    }

    public function retirerFinancement(Partenaire $partenaire, int $formationId): void
    {
        $partenaire->formations()->detach($formationId);
    }
}
