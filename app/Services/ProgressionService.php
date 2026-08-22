<?php

namespace App\Services;

use App\Models\Progression;

class ProgressionService
{
    //liste (filtrable par user_id et/ou lecon_id)
    public function getAll(array $filters = [])
    {
        $query = Progression::with(['user', 'lecon']);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['lecon_id'])) {
            $query->where('lecon_id', $filters['lecon_id']);
        }

        return $query->latest()->get();
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