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
     * liste (filtrable par user_id et/ou lecon_id)
     */
    public function index(\Illuminate\Http\Request $request)
    {
        // SÉCURITÉ: même faille — un étudiant pouvait voir la
        // progression (avancement leçon par leçon) de n'importe quel
        // autre utilisateur via ?user_id=.
        $filters = $request->only(['user_id', 'lecon_id']);
        if (in_array($request->user()->role, ['etudiant', 'partenaire'])) {
            $filters['user_id'] = $request->user()->id;
        }

        return response()->json([
            'success'=>true,

            'data'=>
            $this
            ->progressionService
            ->getAll($filters)
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