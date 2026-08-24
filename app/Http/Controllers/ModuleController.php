<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\UpdateModuleRequest;
use App\Services\ModuleService;
use App\Traits\ChecksFormationOwnership;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    use ChecksFormationOwnership;

    public function __construct(protected ModuleService $moduleService)
    {}


    // Liste des modules (un formateur ne voit que ceux de ses propres
    // formations, un étudiant ceux des formations où il est inscrit
    // activement, un admin voit tout ; ?formation_id= reste utilisable
    // pour filtrer davantage)
    public function index(Request $request)
    {
        $query = Module::query();

        if ($request->filled('formation_id')) {
            $query->where('formation_id', $request->input('formation_id'));
        }

        $this->scopeToAccessibleFormations($query);

        return response()->json([
            'success' => true,
            'data'    => $query->get()
        ]);
    }

    // Creer un module
    public function store(StoreModuleRequest $request)
    {
        try{
            //validation
            $data = $request->validated();

            $this->authorizeFormationOwner($data['formation_id']);

            //Verification (unicité DANS la même formation, pas globale)
            $existingModule = Module::where('titre', $data['titre'])
                ->where('formation_id', $data['formation_id'])
                ->exists();
            if ($existingModule){
                return response()->json([
                    'success' => false,
                    'message' => 'Ce module existe deja'
                ], 422);
            }

            //Creation via le service
            $module = $this->moduleService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Module créé avec succès',
                'data'    => $module
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                'message' => 'une erreur inattendue est survenue',
                'error' => $e->getMessage()
            ]);
        }

    }

    // Afficher un module
    public function show(Module $module)
    {
        $this->authorizeFormationAccess($module->formation_id);

        return response()->json([
            'success' => true,
            'data'    => $module->load('lecons')
        ]);
    }


    // Modifier un module
    public function update(UpdateModuleRequest $request, Module $module)
    {
        $this->authorizeFormationOwner($module->formation_id);

        $module = $this->moduleService->update($module, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Module modifié avec succès',
            'data'    => $module
        ]);
    }

    // Supprimer un module
    public function destroy(Module $module)
    {
        $this->authorizeFormationOwner($module->formation_id);

        $this->moduleService->delete($module);

        return response()->json([
            'success' => true,
            'message' => 'Module supprimé avec succès'
        ]);
    }
}
