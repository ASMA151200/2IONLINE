<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscription;
use App\Models\Formation;
use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    public function index()
    {
        return Inscription::with([
            'user',
            'formation'
        ])->get();
    }

    public function inscrire(
        Request $request
    )
    {
        $request->validate([

            'user_id'=>
            'required|exists:users,id',

            'formation_id'=>
            'required|exists:formations,id'

        ]);

        $exists=
        Inscription::where(
            'user_id',
            $request->user_id
        )

        ->where(
            'formation_id',
            $request->formation_id
        )

        ->exists();

        if($exists){

            return response()->json([
                'message'=>
                'Etudiant déjà inscrit'
            ],422);

        }

        $inscription=
        Inscription::create([

            'date'=>now(),

            'statut'=>'actif',

            'user_id'=>
            $request->user_id,

            'formation_id'=>
            $request->formation_id

        ]);

        Formation::where(
            'id',
            $request->formation_id
        )

        ->increment(
            'nb_inscrit'
        );

        return response()->json([
            'message'=>'Inscription créée',
            'data'=>$inscription
        ]);
    }

    public function annuler(
        $id
    )
    {
        $inscription=
        Inscription::findOrFail(
            $id
        );

        $inscription->update([

            'statut'=>'annule'

        ]);

        return response()->json([
            'message'=>'Inscription annulée'
        ]);
    }
}