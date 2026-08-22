<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Http\Requests\StoreExamenRequest;
use App\Http\Requests\UpdateExamenRequest;
use App\Services\ExamenService;
use Illuminate\Http\Request;

class ExamenController extends Controller
{
    public function __construct(protected ExamenService $examenService)
    {
    }

    /**
     * Liste des examens
     */
    public function index()
    {
        $examens = Examen::with('formation')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $examens
        ]);
    }

    /**
     * Créer examen (avec questions/choix imbriqués, optionnel)
     */
    public function store(StoreExamenRequest $request)
    {
        $examen = $this->examenService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Examen créé avec succès',
            'data' => $examen->load('formation')
        ],201);
    }

    /**
     * Afficher un examen
     */
    public function show(Examen $examen)
    {
        return response()->json([
            'success' => true,
            'data' => $examen->load([
                'formation',
                'questions.choix',
                'resultats'
            ])
        ]);
    }

    /**
     * Modifier
     */
    public function update(
        UpdateExamenRequest $request,
        Examen $examen
    )
    {
        $examen->update(
            $request->validated()
        );

        return response()->json([
            'success'=>true,
            'message'=>'Examen modifié avec succès',
            'data'=>$examen
        ]);
    }

    /**
     * Supprimer
     */
    public function destroy(Examen $examen)
    {
        $examen->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Examen supprimé avec succès'
        ]);
    }

    /**
     * Passer un examen (etudiant) — POST /v1/examens/{examen}/soumettre
     * Body attendu: { reponses: [{ question_id, choix_id? }, ...] }
     */
    public function soumettre(Request $request, Examen $examen)
    {
        $data = $request->validate([
            'reponses' => 'required|array|min:1',
            'reponses.*.question_id' => 'required|exists:exercice_questions,id',
            'reponses.*.choix_id' => 'nullable|exists:choix,id',
        ]);

        $resultat = $this->examenService->soumettre($examen, $request->user()->id, $data['reponses']);

        return response()->json([
            'success' => true,
            'message' => 'Examen soumis avec succès',
            'data' => $resultat,
        ], 201);
    }
}