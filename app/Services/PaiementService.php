<?php

namespace App\Services;

use App\Models\Paiement;

class PaiementService
{
    public function getAll(array $filters = [])
    {
        $query = Paiement::with(['user', 'formation']);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['formation_id'])) {
            $query->where('formation_id', $filters['formation_id']);
        }

        return $query->latest()->get();
    }

    public function create(array $data): Paiement
    {
        return Paiement::create($data);
    }

    public function update(Paiement $paiement, array $data): Paiement
    {
        $paiement->update($data);
        return $paiement;
    }

    public function delete(Paiement $paiement): void
    {
        $paiement->delete();
    }
}