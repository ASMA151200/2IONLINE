<?php

namespace App\Services;

use App\Models\Lecon;
use Illuminate\Support\Facades\Storage;

class LeconService
{
    //Liste des lecons
    public function getAll()
    {
        return Lecon::with('module')->latest()->get();
    }

    //Creer une lecon
    public function create(array $data): Lecon
    {
        //Upload video
        if(isset($data['video']))
            {
                $data['video'] = $data['video']->store('lecons/videos', 'public');
            }

        //Upload document
        if(isset($data['document']))
            {
                $data['document'] = $data['document']->store('lecons/documents', 'public');
            }

        return Lecon::create($data);

    }

    //Afficher une lecon
    public function getById(int $id): Lecon
    {
        return Lecon::with('module')->findOrFail($id);
    }

    //Modifier une lecon
    public function update(Lecon $lecon, array $data): Lecon
    {

        //Remplacer une video
        if(isset($data['video']))
            {
                if($lecon->video){
                    Storage::disk('public')->delete($lecon->video);
                }

                $data['video'] = $data['video']->store('lecons/videos','public');

            }

        //Remplacer un document
        if(isset($data['document']))
            {
                if($lecon->document){
                    Storage::disk('public')->delete($lecon->document);
                }

                $data['document'] = $data['document']->store('lecons/documents','public');

            }

        $lecon->update($data);

        return $lecon;

    }

    //Supprimer une lecon
    public function delete(Lecon $lecon): void
    {

        //Supprimer video
        if($lecon->video)
            {
                Storage::disk('public')->delete($lecon->video);
            }

        //Supprimer document
        if($lecon->document)
            {
                Storage::disk('public')->delete($lecon->document);
            }

        $lecon->deleteOrFail();


    }



}



?>
