<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Create a new user (formateur or etudiant)
     * Only admin can create users
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:formateur,etudiant'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user,
        ], 201);
    }

    /**
     * Get all users (admin only)
     */
    public function index()
    {
        return response()->json([
            'users' => User::all(['id', 'name', 'email', 'role', 'created_at']),
        ]);
    }

    /**
     * Get a single user
     */
    public function show(User $user)
    {
        return response()->json(['user' => $user]);
    }

    /**
     * Update a user (admin only)
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['string', 'max:255'],
            'email' => ['email', 'unique:users,email,' . $user->id],
            'role' => ['in:formateur,etudiant,admin'],
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Utilisateur mis à jour',
            'user' => $user,
        ]);
    }

    /**
     * Delete a user (admin only)
     */
    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            throw ValidationException::withMessages([
                'user' => ['Impossible de supprimer un admin'],
            ]);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé']);
    }
}
