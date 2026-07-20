<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    // partie constructeur
    public function __construct(
        protected AuthService $authService
    ){}

    // partie register
    public function register(RegisterRequest $request)
    {
        $data = $this->authService-> register($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Compte créé avec succès',

            'token' => $data['token'],

            'user' => new UserResource($data['user'])
        ], 201);
    }

    //partie connexion
    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        if (!$data){
            return response()->json([
                'success' => false,
                'message' => 'Identifiants invalides'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'token' => $data['token'],
            'user' => new UserResource($data['user'])
        ], 201);
    }

    // partie deconnexion
    public function logout(Request $request){

        $this->authService-> logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Déconnexion réussie'
        ]);
    }

    // partie utilisateur connecté
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ]);
    }

    // Changer son mot de passe (connecté)
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $this->authService->changePassword($request->user(), $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès'
            ]);

        } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
            ], 422);
        }
    }

    // Demander un code de réinitialisation (non connecté)
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $this->authService->forgotPassword($request->email);

            return response()->json([
                'success' => true,
                'message' => 'Code de réinitialisation envoyé par email'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // Réinitialiser le mot de passe avec le code
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->authService->resetPassword($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe réinitialisé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }

    }

}
