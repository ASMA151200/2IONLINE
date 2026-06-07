<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
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


    //partie login
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

}
