<?php

namespace App\Http\Controllers;

use App\Models\Resultat;
use App\Http\Requests\StoreResultatRequest;

class ResultatController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Resultat::with(['user','examen'])->latest()->get()
        ]);
    }

    public function store(StoreResultatRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id();

        $resultat = Resultat::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Résultat enregistré',
            'data' => $resultat
        ], 201);
    }

    public function show(Resultat $resultat)
    {
        return response()->json([
            'data' => $resultat->load(['user','examen'])
        ]);
    }
}