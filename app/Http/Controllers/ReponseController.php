<?php

namespace App\Http\Controllers;

use App\Models\Reponse;
use App\Http\Requests\StoreSingleReponseRequest;
use App\Http\Requests\UpdateReponseRequest;

class ReponseController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Reponse::with('question')->orderBy('created_at')->get()
        ]);
    }

    public function store(StoreSingleReponseRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $reponse = Reponse::create($data);

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