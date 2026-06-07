<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Http\Requests\StoreModuleRequest;
use App\Http\Requests\UpdateModuleRequest;
use App\Services\ModuleService;

class ModuleController extends Controller
{

    public function __construct(protected ModuleService $moduleService)
    {}

    /**
     * //Liste des modules
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data'    => $this->moduleService->getAll()
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
     * Creer un module
     */
    public function store(StoreModuleRequest $request)
    {
        $module = $this->moduleService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Module créé avec succès',
            'data'    => $module
        ], 201);
    }

    /**
     * afficher un module
     */
    public function show(Module $module)
    {
        return response()->json([
            'success' => true,
            'data'    => $module->load('lecons')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Module $module)
    {
        //
    }

    /**
     * Modifier un module
     */
    public function update(UpdateModuleRequest $request, Module $module)
    {
        $module = $this->moduleService->update($module, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Module modifié avec succès',
            'data'    => $module
        ]);
    }

    /**
     * Supprimer un module
     */
    public function destroy(Module $module)
    {
        $this->moduleService->delete($module);

        return response()->json([
            'success' => true,
            'message' => 'Module supprimé avec succès'
        ]);
    }
}
