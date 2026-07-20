<?php

namespace App\Services;

use App\Models\Progression;

class ProgressionService
{
    //liste
    public function getAll()
    {
        return Progression::with([
            'user',
            'lecon'
        ])
        ->latest()
        ->get();
    }


    //creer
    public function create(
        array $data
    ): Progression
    {
        return Progression::create(
            $data
        );
    }


    //modifier
    public function update(
        Progression $progression,
        array $data
    ): Progression
    {

        $progression
        ->update($data);

        return $progression;
    }


    //supprimer
    public function delete(
        Progression $progression
    ): void
    {
        $progression->delete();
    }
}