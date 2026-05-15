<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(
            User::latest()->get()
        );
    }

    public function etudiants()
    {
        return User::where(
            'role',
            'etudiant'
        )->get();
    }

    public function formateurs()
    {
        return User::where(
            'role',
            'formateur'
        )->get();
    }

    public function store(Request $request)
    {
        $validated=$request->validate([

            'prenom'=>'required',
            'nom'=>'required',
            'telephone'=>'required',

            'email'=>
            'required|email|unique:users',

            'password'=>
            'required|min:8',

            'photo'=>'nullable',

            'role'=>
            'required|in:etudiant,formateur'

        ]);

        $validated['password']=
        Hash::make(
            $validated['password']
        );

        $user=
        User::create($validated);

        return response()->json([
            'message'=>'Utilisateur créé',
            'user'=>$user
        ],201);
    }

    public function show(User $user)
    {
        return response()->json(
            $user
        );
    }

    public function update(
        Request $request,
        User $user
    )
    {
        $validated=
        $request->validate([

            'prenom'=>'sometimes',

            'nom'=>'sometimes',

            'telephone'=>'sometimes',

            'email'=>
            'sometimes|email|unique:users,email,'.$user->id,

            'photo'=>'nullable',

            'role'=>
            'sometimes|in:admin,formateur,etudiant'
        ]);

        $user->update(
            $validated
        );

        return response()->json([
            'message'=>'Utilisateur modifié',
            'user'=>$user
        ]);
    }

    public function destroy(User $user)
    {
        if(
            $user->role=="admin"
        ){

            throw ValidationException::withMessages([
                'user'=>[
                    'Impossible de supprimer admin'
                ]
            ]);

        }

        $user->delete();

        return response()->json([
            'message'=>'Utilisateur supprimé'
        ]);
    }
}