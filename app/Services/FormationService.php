<?php

namespace App\Services;

use App\Models\Formation;
use Illuminate\Support\Facades\Storage;

class FormationService
{
    //Liste des formations
    public function getAll()
    {
        return Formation::with('modules.lecons')->latest()->get();
    }

    //Creer une formation
    public function create(array $data): Formation
    {


        //Upload image
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('formations/images', 'public');
        }

        return Formation::create($data);
    }

    //Afficher une formation
    public function getById(int $id): Formation
    {
        return Formation::with('modules.lecons')->findOrFail($id);
    }

    //Modifier une formation
    public function update(Formation $formation, array $data): Formation
    {
        //Remplacer image
        if (isset($data['image'])) {
            if ($formation->image) {
                Storage::disk('public')->delete($formation->image);
            }
            $data['image'] = $data['image']->store('formations/images', 'public');
        }

        $formation->update($data);

        return $formation;
    }

    //Supprimer une formation
    public function delete(Formation $formation): void
    {
        //Supprimer image
        if ($formation->image) {
            Storage::disk('public')->delete($formation->image);
        }

        $formation->deleteOrFail();
    }
}

?>
