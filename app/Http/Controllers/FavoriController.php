<?php

namespace App\Http\Controllers;

use App\Models\Favori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriController extends Controller
{
    /** GET /v1/favoris — favoris de l'utilisateur connecté */
    public function index(Request $request): JsonResponse
    {
        $favoris = Favori::where('user_id', $request->user()->id)
            ->with(['lecon', 'formation'])
            ->get();

        return response()->json(['success' => true, 'data' => $favoris]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lecon_id' => 'required|exists:lecons,id',
            'formation_id' => 'required|exists:formations,id',
        ]);
        $data['user_id'] = $request->user()->id;

        // Évite le doublon si déjà en favori (contrainte unique
        // user_id+lecon_id) — renvoie simplement l'existant.
        $favori = Favori::firstOrCreate(
            ['user_id' => $data['user_id'], 'lecon_id' => $data['lecon_id']],
            ['formation_id' => $data['formation_id']],
        );

        return response()->json(['success' => true, 'message' => 'Ajouté aux favoris', 'data' => $favori], 201);
    }

    public function destroy(Request $request, Favori $favori): JsonResponse
    {
        if ($favori->user_id !== $request->user()->id) {
            abort(403, 'Accès interdit');
        }

        $favori->delete();

        return response()->json(['success' => true, 'message' => 'Retiré des favoris']);
    }
}
