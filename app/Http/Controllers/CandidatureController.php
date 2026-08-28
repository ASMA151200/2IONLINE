<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Opportunite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CandidatureController extends Controller
{
    /** Postuler à une opportunité (étudiant connecté) */
    public function store(Request $request, Opportunite $opportunite): JsonResponse
    {
        $data = $request->validate(['message' => 'nullable|string|max:1000']);

        if (Candidature::where('opportunite_id', $opportunite->id)->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Vous avez déjà postulé à cette opportunité.'], 422);
        }

        $candidature = Candidature::create([
            'opportunite_id' => $opportunite->id,
            'user_id' => $request->user()->id,
            'message' => $data['message'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Candidature envoyée avec succès', 'data' => $candidature], 201);
    }

    /** Mes candidatures (étudiant connecté) */
    public function mesCandidatures(Request $request): JsonResponse
    {
        $candidatures = Candidature::with('opportunite')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $candidatures]);
    }

    /** Candidatures reçues pour une opportunité (admin uniquement) */
    public function index(Request $request, Opportunite $opportunite): JsonResponse
    {
        $candidatures = Candidature::with('user')
            ->where('opportunite_id', $opportunite->id)
            ->latest()
            ->get();

        return response()->json(['success' => true, 'data' => $candidatures]);
    }

    /** Changer le statut d'une candidature (admin uniquement) */
    public function updateStatut(Request $request, Candidature $candidature): JsonResponse
    {
        $data = $request->validate(['statut' => 'required|in:envoyee,vue,acceptee,refusee']);
        $candidature->update($data);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour', 'data' => $candidature]);
    }
}
