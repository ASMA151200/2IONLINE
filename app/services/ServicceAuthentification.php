use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

<?php

namespace App\Services;


class ServicceAuthentification
{
    /**
     * Authentifier un utilisateur
     */
    public function login(string $email, string $password): bool
    {
        return Auth::attempt(['email' => $email, 'password' => $password]);
    }

    /**
     * Créer un nouvel utilisateur
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Déconnecter l'utilisateur
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Obtenir l'utilisateur authentifié
     */
    public function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    /**
     * Vérifier si l'utilisateur est authentifié
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Changer le mot de passe
     */
    public function changePassword(User $user, string $newPassword): bool
    {
        $user->update(['password' => Hash::make($newPassword)]);
        return true;
    }
}
