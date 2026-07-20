<?php

namespace App\Services;

use App\Models\Paiement;

class PaiementService
{
    public function getAll()
    {
        return Paiement::with([
            'user',
            'formation'
        ])->latest()->get();
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