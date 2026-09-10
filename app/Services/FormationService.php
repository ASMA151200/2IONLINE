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
    // $userId optionnel : filtre sur les formations dont ce user est
    // propriétaire (formations.user_id) — utilisé par le frontend
    // professeur (/formations?user_id=X) pour ne lister QUE ses propres
    // formations, plutôt que celles de toute la plateforme. Ce filtre
    // n'était auparavant jamais appliqué côté backend (le paramètre était
    // silencieusement ignoré), ce qui permettait à un professeur de
    // choisir une formation qu'il ne possède pas dans les sélecteurs
    // module/leçon/exercice/examen, pour ensuite se faire systématiquement
    // rejeter par ChecksFormationOwnership au moment d'enregistrer.
    public function getAll(?int $userId = null)
    {
        $query = Formation::with(['modules' => function ($q) {
            $q->select('id', 'titre', 'ordre', 'formation_id')->orderBy('ordre');
        }])->latest();

        // CORRIGÉ: filtrait auparavant uniquement sur formations.user_id
        // (le seul "propriétaire principal") — un formateur autorisé à
        // intervenir dans une formation SANS en être le propriétaire
        // principal (table pivot formation_formateur, modèle plusieurs-
        // à-plusieurs) ne la voyait alors dans AUCUNE de ses pages de
        // gestion (leçons, modules, exercices, examens, sessions live —
        // toutes utilisent ce même endpoint pour "mes formations").
        if ($userId !== null) {
            $query->whereHas('formateurs', fn ($q) => $q->where('users.id', $userId));
        }

        return $query->get();
    }

    //Creer une formation
    public function create(array $data): Formation
    {


        //Upload image
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('formations/images', 'public');
        }

        $formation = Formation::create($data);

        // Si c'est un formateur (pas un admin) qui crée cette formation,
        // on l'ajoute automatiquement à la table pivot
        // formation_formateur — sinon, avec le nouveau modèle plusieurs-
        // à-plusieurs, il n'aurait paradoxalement pas le droit de gérer
        // le contenu de la formation qu'il vient lui-même de créer
        // (formations.user_id seul n'est plus la source de vérité pour
        // l'accès, voir ChecksFormationOwnership).
        if (!empty($data['user_id'])) {
            $createur = \App\Models\User::find($data['user_id']);
            if ($createur && $createur->role === 'formateur') {
                $formation->formateurs()->syncWithoutDetaching([$createur->id]);
            }
        }

        return $formation;
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
