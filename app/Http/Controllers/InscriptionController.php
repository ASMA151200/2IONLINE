<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Http\Requests\StoreInscriptionRequest;
use App\Http\Requests\UpdateInscriptionRequest;
use App\Services\InscriptionService;

class InscriptionController extends Controller
{
    public function __construct(
        protected InscriptionService $inscriptionService
    ){}

    /**
     * liste
     */
    public function index()
    {
        return response()->json([
            'success'=>true,
            'data'=>$this
                    ->inscriptionService
                    ->getAll()
        ]);
    }

    /**
     * creer inscription
     */
    public function store(
        StoreInscriptionRequest $request
    )
    {

        $data = $request->validated();

        // utilisateur connecté
        $data['user_id']=auth()->id();

        //statut par défaut
        $data['statut'] =
        $data['statut'] ?? 'actif';


        //Empêcher double inscription
        $exist = Inscription::where(
                    'user_id',
                    auth()->id()
                )
                ->where(
                    'formation_id',
                    $data['formation_id']
                )
                ->exists();

        if($exist){

            return response()->json([
                'success'=>false,
                'message'=>
                'Vous êtes déjà inscrit'
            ],409);
        }


        $inscription =
        $this->inscriptionService
        ->create($data);

        return response()->json([
            'success'=>true,
            'message'=>
            'Inscription effectuée avec succès',

            'data'=>$inscription->load([
                'user',
                'formation'
            ])
        ],201);
    }


    /**
     * afficher
     */
    public function show(
        Inscription $inscription
    )
    {
        return response()->json([
            'success'=>true,
            'data'=>
            $inscription->load([
                'user',
                'formation',
                'paiements'
            ])
        ]);
    }

    /**
     * modifier
     */
    public function update(
        UpdateInscriptionRequest $request,
        Inscription $inscription
    )
    {

        $inscription =
        $this->inscriptionService
        ->update(
            $inscription,
            $request->validated()
        );

        return response()->json([
            'success'=>true,
            'message'=>
            'Inscription modifiée',

            'data'=>$inscription
        ]);
    }

    /**
     * supprimer
     */
    public function destroy(
        Inscription $inscription
    )
    {
        $this->inscriptionService
        ->delete($inscription);

        return response()->json([
            'success'=>true,
            'message'=>
            'Inscription supprimée'
        ]);
    }
}