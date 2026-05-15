<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        return Module::with(
            'formation'
        )->get();
    }

    public function store(
        Request $request
    )
    {
        $validated=
        $request->validate([

            'titre'=>'required',

            'description'=>'nullable',

            'ordre'=>'required',

            'formation_id'=>
            'required|exists:formations,id'

        ]);

        return Module::create(
            $validated
        );
    }

    public function destroy(
        Module $module
    )
    {
        $module->delete();

        return response()->json([
            'message'=>'Supprimé'
        ]);
    }
}