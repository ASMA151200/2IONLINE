<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Http\Requests\StoreExamenRequest;
use App\Http\Requests\UpdateExamenRequest;

class ExamenController extends Controller
{
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
     * Créer examen
     */
    public function store(StoreExamenRequest $request)
    {
        $examen = Examen::create(
            $request->validated()
        );

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
                'questions',
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
}