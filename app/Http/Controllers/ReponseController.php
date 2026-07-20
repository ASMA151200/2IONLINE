<?php

namespace App\Http\Controllers;

use App\Models\Reponse;
use App\Http\Requests\StoreReponseRequest;
use App\Http\Requests\UpdateReponseRequest;

class ReponseController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Reponse::with('question')->orderBy('ordre')->get()
        ]);
    }

    public function store(StoreReponseRequest $request)
    {
        $reponse = Reponse::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Réponse créée',
            'data' => $reponse
        ], 201);
    }

    public function update(UpdateReponseRequest $request, Reponse $reponse)
    {
        $reponse->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Réponse modifiée',
            'data' => $reponse
        ]);
    }

    public function destroy(Reponse $reponse)
    {
        $reponse->delete();

        return response()->json([
            'success' => true,
            'message' => 'Réponse supprimée'
        ]);
    }
}