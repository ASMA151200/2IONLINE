<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Http\Requests\StoreCategorieRequest;
use App\Http\Requests\UpdateCategorieRequest;
use App\Services\CategorieService;


class CategorieController extends Controller
{

    public function __construct(protected CategorieService $categorieService)
    {}

    /**
     * Liste des categories
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data'    => $this->categorieService->getAll()
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
     * Creer une categorie
     */
    public function store(StoreCategorieRequest $request)
    {
        $data = $request->validated();

        $categorie = $this->categorieService->create($data);

        return response()->json([
            'success' => true,
            'Message' => 'Categorie cree avec succes',
            'data' => $categorie
        ], 201);
    }

    /**
     * Afficher une categorie
     */
    public function show(Categorie $categorie)
    {
         return response()->json([
            'success' => true,
            'data' => $categorie->load('formations')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorie $categorie)
    {
        //
    }

    /**
     * Modifier une categorie
     */
    public function update(UpdateCategorieRequest $request, Categorie $categorie)
    {
        $data = $request->validated();

        $categorie = $this->categorieService->update($categorie, $data);

        return response()->json([
            'success' => true,
            'message' => 'Categorie modifiée avec succès',
            'data'    => $categorie
        ]);
    }

    /**
     * Supprimer une fprmation
     */
    public function destroy(Categorie $categorie)
    {
        $this->categorieService->delete($categorie);

        return response()->json([
            'success' => true,
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }
}
