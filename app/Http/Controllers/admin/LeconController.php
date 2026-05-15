<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecon;
use Illuminate\Http\Request;

class LeconController extends Controller
{
    public function index()
    {
        return Lecon::with(
            'module'
        )->get();
    }

    public function store(
        Request $request
    )
    {
        $validated=
        $request->validate([

            'titre'=>'required',

            'contenu'=>'required',

            'video'=>'nullable',

            'document'=>'nullable',

            'ordre'=>'required',

            'module_id'=>
            'required|exists:modules,id'

        ]);

        return Lecon::create(
            $validated
        );
    }

    public function destroy(
        Lecon $lecon
    )
    {
        $lecon->delete();

        return response()->json([
            'message'=>'Leçon supprimée'
        ]);
    }
}