<?php

namespace App\Http\Controllers;

use App\Models\Progression;
use App\Services\ProgressionService;
use App\Http\Requests\StoreProgressionRequest;
use App\Http\Requests\UpdateProgressionRequest;

class ProgressionController extends Controller
{

    public function __construct(
        protected ProgressionService
        $progressionService
    ){}

    /**
     * liste
     */
    public function index()
    {
        return response()->json([
            'success'=>true,

            'data'=>
            $this
            ->progressionService
            ->getAll()
        ]);
    }

    /**
     * creer
     */
    public function store(
        StoreProgressionRequest $request
    )
    {

        $data=
        $request->validated();

        //user connecté
        $data['user_id']
        = auth()->id();


        //Empêcher doublon
        $exist=
        Progression::where(
            'user_id',
            auth()->id()
        )
        ->where(
            'lecon_id',
            $data['lecon_id']
        )
        ->exists();

        if($exist){

            return response()->json([
                'success'=>false,
                'message'=>
                'Progression déjà créée'
            ],409);
        }

        $progression=
        $this
        ->progressionService
        ->create($data);

        return response()->json([

            'success'=>true,

            'message'=>
            'Progression créée avec succès',

            'data'=>
            $progression->load([
                'user',
                'lecon'
            ])

        ],201);
    }


    /**
     * afficher
     */
    public function show(
        Progression $progression
    )
    {

        return response()->json([

            'success'=>true,

            'data'=>
            $progression->load([
                'user',
                'lecon'
            ])

        ]);
    }


    /**
     * modifier
     */
    public function update(
        UpdateProgressionRequest $request,
        Progression $progression
    )
    {

        $progression=
        $this
        ->progressionService
        ->update(
            $progression,
            $request->validated()
        );


        return response()->json([

            'success'=>true,

            'message'=>
            'Progression modifiée',

            'data'=>
            $progression

        ]);
    }


    /**
     * supprimer
     */
    public function destroy(
        Progression $progression
    )
    {

        $this
        ->progressionService
        ->delete(
            $progression
        );

        return response()->json([

            'success'=>true,

            'message'=>
            'Progression supprimée'

        ]);
    }
}