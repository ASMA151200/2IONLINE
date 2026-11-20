<?php

namespace App\Services;

use App\Models\Inscription;

class InscriptionService
{
    //liste (filtrable par user_id et/ou formation_id)
    public function getAll(array $filters = [])
    {
        $query = Inscription::with(['user', 'formation']);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['formation_id'])) {
            $query->where('formation_id', $filters['formation_id']);
        }

        return $query->latest()->get();
    }


    //creer
    public function create(array $data): Inscription
    {
        return Inscription::create($data);
    }


    //modifier
    public function update(
        Inscription $inscription,
        array $data
    ): Inscription
    {
        $statut = $data['statut'] ?? $inscription->statut;
        $formationId = (int) ($data['formation_id'] ?? $inscription->formation_id);

        if ($statut === 'actif'
            && ($conflit = Inscription::conflitFormationActive($inscription->user_id, $formationId, $inscription->id))) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'statut' => Inscription::messageUneSeuleFormation($conflit),
            ]);
        }

        $inscription->update($data);

        return $inscription;
    }


    //supprimer
    public function delete(
        Inscription $inscription
    ): void
    {
        $inscription->delete();
    }
}