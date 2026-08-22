<?php

namespace App\Http\Controllers;

use App\Models\Resultat;
use App\Http\Requests\StoreResultatRequest;

class ResultatController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Resultat::with(['user', 'examen']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }
        if ($request->filled('examen_id')) {
            $query->where('examen_id', $request->input('examen_id'));
        }

        return response()->json([
            'data' => $query->latest()->get()
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