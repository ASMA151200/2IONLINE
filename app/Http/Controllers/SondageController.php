<?php

namespace App\Http\Controllers;

use App\Models\Sondage;
use App\Models\SondageReponse;
use App\Traits\ChecksFormationOwnership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SondageController extends Controller
{
    use ChecksFormationOwnership;

    /** Créer un sondage pour une formation (formateur/admin propriétaire) */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'titre' => 'required|string|max:255',
            'questions' => 'required|array|min:1',
            'questions.*.id' => 'required|string',
            'questions.*.texte' => 'required|string',
            'questions.*.type' => 'required|in:note,texte',
        ]);

        $this->authorizeFormationOwner($data['formation_id']);

        $sondage = Sondage::create($data);

        return response()->json(['success' => true, 'message' => 'Sondage créé avec succès', 'data' => $sondage], 201);
    }

    /** Sondages disponibles pour une formation (étudiant inscrit, formateur/admin propriétaire) */
    public function index(Request $request): JsonResponse
    {
        $formationId = $request->query('formation_id');
        if (!$formationId) {
            return response()->json(['success' => false, 'message' => 'formation_id requis'], 422);
        }

        $this->authorizeFormationAccess($formationId);

        return response()->json([
            'success' => true,
            'data' => Sondage::where('formation_id', $formationId)->get(),
        ]);
    }

    /** Répondre à un sondage (étudiant) */
    public function repondre(Request $request, Sondage $sondage): JsonResponse
    {
        $this->authorizeFormationAccess($sondage->formation_id);

        $data = $request->validate([
            'reponses' => 'required|array|min:1',
            'reponses.*.question_id' => 'required|string',
            'reponses.*.valeur' => 'required',
        ]);

        $reponse = SondageReponse::updateOrCreate(
            ['sondage_id' => $sondage->id, 'user_id' => $request->user()->id],
            ['reponses' => $data['reponses']],
        );

        return response()->json(['success' => true, 'message' => 'Réponse enregistrée avec succès', 'data' => $reponse], 201);
    }

    /** Résultats agrégés d'un sondage (formateur/admin propriétaire) */
    public function resultats(Request $request, Sondage $sondage): JsonResponse
    {
        $this->authorizeFormationOwner($sondage->formation_id);

        $reponses = $sondage->reponses()->with('user')->get();

        return response()->json(['success' => true, 'data' => ['sondage' => $sondage, 'reponses' => $reponses]]);
    }
}
