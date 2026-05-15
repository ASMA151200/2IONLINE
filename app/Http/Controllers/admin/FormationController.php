<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function index()
    {
        return Formation::with([
            'formateur',
            'categorie'
        ])->get();
    }

    public function store(Request $request)
    {
        $validated=
        $request->validate([

            'titre'=>'required',

            'description'=>'required',

            'image'=>'nullable',

            'formateur_id'=>
            'required|exists:users,id',

            'niveau'=>'required',

            'duree'=>'required',

            'prix'=>'required',

            'statut'=>
            'required|in:en ligne,presentiel,hybride',

            'categorie_id'=>
            'required|exists:categories,id'

        ]);

        $formation=
        Formation::create(
            $validated
        );

        return response()->json([
            'message'=>'Formation créée',
            'formation'=>$formation
        ],201);
    }

    public function show(
        Formation $formation
    )
    {
        return $formation
        ->load([
            'modules',
            'formateur',
            'categorie'
        ]);
    }

    public function update(
        Request $request,
        Formation $formation
    )
    {
        $formation->update(
            $request->all()
        );

        return response()->json([
            'message'=>'Formation modifiée'
        ]);
    }

    public function destroy(
        Formation $formation
    )
    {
        $formation->delete();

        return response()->json([
            'message'=>'Formation supprimée'
        ]);
    }
}