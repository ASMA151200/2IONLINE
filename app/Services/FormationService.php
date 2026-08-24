<?php

namespace App\Services;

use App\Models\Formation;
use Illuminate\Support\Facades\Storage;

class FormationService
{
    //Liste des formations — ne charge QUE les titres/ordre des modules,
    // jamais le contenu des leçons (vidéo/document/contenu). Cette
    // méthode alimente à la fois la liste publique et la liste admin :
    // charger '.lecons' en entier ici avait le même défaut que celui
    // corrigé sur show() (fuite de contenu payant à quiconque), ET
    // dégradait fortement les performances (toutes les leçons de toutes
    // les formations, à chaque chargement de liste) — probable cause de
    // lenteurs/timeouts (504) observés lors du rafraîchissement de la
    // liste après une modification.
    public function getAll()
    {
        return Formation::with(['modules' => function ($q) {
            $q->select('id', 'titre', 'ordre', 'formation_id')->orderBy('ordre');
        }])->latest()->get();
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

    //Afficher une formation (usage interne — le endpoint public show()
    // du contrôleur fait sa propre requête restreinte, voir
    // FormationController::show())
    public function getById(int $id): Formation
    {
        return Formation::with(['modules' => function ($q) {
            $q->select('id', 'titre', 'ordre', 'formation_id')->orderBy('ordre');
        }])->findOrFail($id);
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
