<?php

namespace App\Http\Controllers;

use App\Models\Exercice;
use App\Models\Lecon;
use App\Models\Reponse;
use App\Http\Requests\StoreExerciceRequest;
use App\Http\Requests\UpdateExerciceRequest;
use App\Http\Requests\StoreReponseRequest;
use App\Http\Requests\CorrigerReponseRequest;
use App\Services\ExerciceService;
use App\Traits\ChecksFormationOwnership;
use Illuminate\Http\Request;

class ExerciceController extends Controller
{
    use ChecksFormationOwnership;

    public function __construct(protected ExerciceService $exerciceService)
    {}

    // Liste des exercices d'une leçon
    public function index(Request $request)
    {
        $leconId = $request->query('lecon_id');

        if (!$leconId) {
        return response()->json([
            'success' => false,
            'message' => 'Le paramètre lecon_id est requis'
        ], 422);
        }

        $lecon = Lecon::with('module')->findOrFail($leconId);
        $this->authorizeFormationAccess($lecon->module->formation_id);

        return response()->json([
            'success' => true,
            'data'    => $this->exerciceService->getByLecon($leconId)
        ]);
    }

    // Créer un exercice (formateur/admin, propriétaire de la formation)
    public function store(StoreExerciceRequest $request)
    {
        try {
            $data = $request->validated();
            $lecon = Lecon::with('module')->findOrFail($data['lecon_id']);
            $this->authorizeFormationOwner($lecon->module->formation_id);

            $exercice = $this->exerciceService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Exercice créé avec succès',
                'data'    => $exercice
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'une erreur inattendue est survenue',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    // Afficher un exercice
    public function show(Exercice $exercice)
    {
        $exercice->load('lecon.module');
        $this->authorizeFormationAccess($exercice->lecon->module->formation_id);

        return response()->json([
            'success' => true,
            'data'    => $exercice->load('questions.choix')
        ]);
    }

    //modifier un exercice
    public function update(UpdateExerciceRequest $request, Exercice $exercice)
    {
        $exercice->load('lecon.module');
        $this->authorizeFormationOwner($exercice->lecon->module->formation_id);

        $exercice = $this->exerciceService->update($exercice, $request->validated());

        return response()->json([
            'success' => true,
            'message'  => 'Exercice mis à jour avec succès.',
            'data' => $exercice,
        ]);
    }

    // Supprimer un exercice (formateur/admin, propriétaire de la formation)
    public function destroy(Exercice $exercice)
    {
        $exercice->load('lecon.module');
        $this->authorizeFormationOwner($exercice->lecon->module->formation_id);

        $this->exerciceService->delete($exercice);

        return response()->json([
            'success' => true,
            'message' => 'Exercice supprimé avec succès'
        ]);
    }

    // Soumettre les réponses (par etudiant)
    public function soumettre(StoreReponseRequest $request, Exercice $exercice)
    {
        $exercice->load('lecon.module');
        $this->authorizeFormationAccess($exercice->lecon->module->formation_id);

        try {
            $resultat = $this->exerciceService->soumettre(
                $exercice,
                $request->user()->id,
                $request->validated()['reponses']
            );

            return response()->json([
                'success'     => true,
                'message'     => 'Réponses soumises avec succès',
                'score_total' => $resultat['score_total'],
                'data'        => $resultat['reponses']
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur inattendue est survenue',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    // Correction manuelle (formateur/admin)
    public function corriger(CorrigerReponseRequest $request, Reponse $reponse)
    {
        $reponse = $this->exerciceService->corriger($reponse, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Réponse corrigée avec succès',
            'data'    => $reponse
        ]);
    }

    // Résultats d'un etudiant (etudiant/formateur/admin)
    public function resultats(Request $request, Exercice $exercice)
    {
        $exercice->load('lecon.module');
        $this->authorizeFormationAccess($exercice->lecon->module->formation_id);

        $userId = $request->query('user_id', $request->user()->id);

        return response()->json([
            'success' => true,
            'data'    => $this->exerciceService->resultats($exercice, $userId)
        ]);
    }
}
