<?php

namespace App\Http\Controllers;

use App\Models\Exercice;
use App\Models\Reponse;
use App\Http\Requests\StoreExerciceRequest;
use App\Http\Requests\UpdateExerciceRequest;
use App\Http\Requests\StoreReponseRequest;
use App\Http\Requests\CorrigerReponseRequest;
use App\Services\ExerciceService;
use Illuminate\Http\Request;

class ExerciceController extends Controller
{
    public function __construct(protected ExerciceService $exerciceService)
    {}

    // Liste des exercices d'une leçon
    public function index(Request $request)
    {
        $leconId = $request->query('lecon_id');

        return response()->json([
            'success' => true,
            'data'    => $this->exerciceService->getByLecon($leconId)
        ]);
    }

    // Créer un exercice (formateur/admin)
    public function store(StoreExerciceRequest $request)
    {
        try {
            $exercice = $this->exerciceService->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Exercice créé avec succès',
                'data'    => $exercice
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // Afficher un exercice
    public function show(Exercice $exercice)
    {
        return response()->json([
            'success' => true,
            'data'    => $exercice->load('questions.choix')
        ]);
    }

    // Supprimer un exercice (formateur/admin)
    public function destroy(Exercice $exercice)
    {
        $this->exerciceService->delete($exercice);

        return response()->json([
            'success' => true,
            'message' => 'Exercice supprimé avec succès'
        ]);
    }

    // Soumettre les réponses (par etudiant)
    public function soumettre(StoreReponseRequest $request, Exercice $exercice)
    {
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
                'success' => false,
                'message' => $e->getMessage()
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
        $userId = $request->query('user_id', $request->user()->id);

        return response()->json([
            'success' => true,
            'data'    => $this->exerciceService->resultats($exercice, $userId)
        ]);
    }
}
