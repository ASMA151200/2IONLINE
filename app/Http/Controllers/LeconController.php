<?php

namespace App\Http\Controllers;

use App\Models\Lecon;
use App\Http\Requests\StoreLeconRequest;
use App\Http\Requests\UpdateLeconRequest;
use App\Services\LeconService;

class LeconController extends Controller
{

    public function __construct(protected LeconService $leconService)
    {}

    /**
     * Liste des lecons
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->leconService->getAll()
        ]);
    }

    /**
     * Ajouter une lecon
     */
    public function store(StoreLeconRequest $request )
    {
        $data = $request->validated();

        if ($request->hasFile('video')) {

            $data['video'] =
                $request->file('video');
        }

        if ($request->hasFile('document')) {

            $data['document'] =
                $request->file('document');
        }

        $lecon = $this->leconService->create($data);

        return response()->json([
            'success' => true,
            'message' =>'Leçon créée avec succès',
            'data' => $lecon

        ], 201);
    }

    /**
     * Afficher une lecon
     */
    public function show(Lecon $lecon)
    {
        return response()->json([
            'success' => true,
            'data' => $lecon->load('module')
        ]);
    }

    /**
     * Modifier une lecon
     */
    public function update(UpdateLeconRequest $request, Lecon $lecon)
    {
        $data = $request->validated();

        if ($request->hasFile('video')) {

            $data['video'] = $request->file('video');
        }

        if ($request->hasFile('document')) {

            $data['document'] = $request->file('document');
        }

        $lecon = $this->leconService->update($lecon, $data);

        return response()->json([
            'success' => true,
            'message' =>'Leçon modifiée avec succès',
            'data' => $lecon
        ]);
    }

    /**
     * Supprimer une lecon
     */
    public function destroy(Lecon $lecon)
    {
        $this->leconService->delete($lecon);

        return response()->json([
            'success' => true,
            'message' =>'Leçon supprimée avec succès'
        ]);
    }
}
