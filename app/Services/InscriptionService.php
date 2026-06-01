<?php

namespace App\Services;

use App\Models\Inscription;

class InscriptionService
{
    //liste
    public function getAll()
    {
        return Inscription::with([
            'user',
            'formation'
        ])->latest()->get();
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