<?php

namespace App\Http\Controllers;

use App\Models\Formateur;
use App\Http\Requests\StoreFormateurRequest;
use App\Http\Requests\UpdateFormateurRequest;
use App\Services\FormateurService;

class FormateurController extends Controller
{

    public function __construct( protected FormateurService $formateurService)
    {
        $this->formateurService = $formateurService;
    }
    /**
     * Liste des formateurs
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->formateurService->getAll()
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
     * Creer un formateur
     */
    public function store(StoreFormateurRequest $request)
    {
        $data = $this->formateurService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Formateur créé avec succès',
            'password_temporaire' => $data['password'],
            'data' => $data['formateur']

        ], 201);
    }

    /**
     * Afficher un formateur
     */
    public function show(Formateur $formateur)
    {
        return response()->json([
            'success' => true,
            'data' => $formateur->load(['user', 'modules'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Formateur $formateur)
    {
        //
    }

    /**
     * Update formateur
     */
    public function update(UpdateFormateurRequest $request, Formateur $formateur)
    {
        $formateur = $this->formateurService->update( $formateur, $request->validated());

        return response()->json([
            'success' => true,
            'message' =>'Formateur modifié avec succès',
            'data' => $formateur
        ], 201);


    }

    /**
     * supprimer formateur
     */
    public function destroy(Formateur $formateur)
    {
        $this->formateurService->destroy($formateur);

        return response()->json([
            'success' => true,
            'message' =>'Formateur supprimé'
        ], 201);

    }


}
