<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Http\Requests\StoreFormationRequest;
use App\Http\Requests\UpdateFormationRequest;
use App\Services\FormationService;

class FormationController extends Controller
{
    public function __construct(protected FormationService $formationService)
    {}
    /**
     * Liste des formations
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data'    => $this->formationService->getAll()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Creer une formation
     */
    public function store(StoreFormationRequest $request)
    {
        try{
            //validation
            $data = $request->validated();

            //verification
            $existingFormation = Formation::where('titre', $data['titre'])->exists();
            if($existingFormation){
                return response()->json([
                    'message' => 'Cette formation existe deja'
                ]);
            }

            //creation via le service
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $data['user_id'] = $request->user()->id; // si user s'est authentifie
            //$data['user_id'] = $request->user_id; // si user ne s'est pas authentifie

            $formation = $this->formationService->create($data);
            return response()->json([
                'success' => true,
                'message' => 'Formation créée avec succès',
                'data'    => $formation
            ], 201);

        }catch(\Exception $e){

            return response()->json([
                'message' => 'une erreur inattendue est survenue',
                'error' => $e->getMessage()
            ]);

        }

    }

    /**
     * afficher une formation
     */
    public function show(Formation $formation)
    {
        return response()->json([
            'success' => true,
            'data'    => $formation->load('modules.lecons')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Formation $formation)
    {
        //
    }

    /**
     * Modifier une formation
     */
    public function update(UpdateFormationRequest $request, Formation $formation)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image');
        }

        $formation = $this->formationService->update($formation, $data);

        return response()->json([
            'success' => true,
            'message' => 'Formation modifiée avec succès',
            'data'    => $formation
        ], 201);
    }

    /**
     * Supprimer une formation
     */
    public function destroy(Formation $formation)
    {
        $this->formationService->delete($formation);

        return response()->json([
            'success' => true,
            'message' => 'Formation supprimée avec succès'
        ], 201);
    }


}
