<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\ResetPasswordCode;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{

    //fonction register (inscription)
    public function register(array $data)
    {
        $user = User::create([
            'prenom' => $data['prenom'],
            'nom' => $data['nom'],
            'telephone' => $data['telephone'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => UserRole::admin->value
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    //fonction login (connexion)
    public function login(array $data)
    {
        if (!Auth::attempt($data)) {
            return null;
        }
        $user = User::query()
                    ->where('email',$data['email'])->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'user' => $user,
            'token' => $token
        ];
    }

    // fonction logout(deconnexion)
    public function logout(User $user)
    {
        $user->tokens()->delete();
    }

    // Changer son propre mot de passe (connecté)
    public function changePassword(User $user, array $data): bool
    {
        // Vérifier l'ancien mot de passe
        if (!Hash::check($data['ancien_password'], $user->password)) {
            throw new \Exception('L\'ancien mot de passe est incorrect');
        }

        $user->update([
            'password' => Hash::make($data['nouveau_password'])
        ]);

        return true;
    }

    // Envoyer le code de réinitialisation
    public function forgotPassword(string $email): void
    {
        $user = User::where('email', $email)->firstOrFail();

        // Supprimer les anciens codes
        ResetPasswordCode::where('email', $email)->delete();

        // Générer un code à 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Sauvegarder le code
        ResetPasswordCode::create([
            'email'     => $email,
            'code'      => $code,
            'expire_at' => now()->addMinutes(15),
        ]);

        // Envoyer le mail
        Mail::to($email)->send(new ResetPasswordMail($code, $user->prenom));
    }

    // Réinitialiser le mot de passe avec le code
    public function resetPassword(array $data): bool
    {
        // Vérifier le code
        $resetCode = ResetPasswordCode::where('email', $data['email'])
                                    ->where('code', $data['code'])
                                    ->latest()
                                    ->first();

        if (!$resetCode) {
            throw new \Exception('Code invalide');
        }

        if ($resetCode->isExpired()) {
            $resetCode->delete();
            throw new \Exception('Code expiré, veuillez en demander un nouveau');
        }

        // Mettre à jour le mot de passe
        User::where('email', $data['email'])->update([
            'password' => Hash::make($data['nouveau_password'])
        ]);

        // Supprimer le code utilisé
        $resetCode->delete();

        return true;

    }

}

