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
     * Creer une categorie
     */
    public function store(StoreCategorieRequest $request)
    {
        try{
            //validation
            $data = $request->validated();

            //verifier si la categorie existe
            $existingCategorie = Categorie::where('titre', $data['titre'])->exists();
            if($existingCategorie){
                return response()->json([
                    'message' => 'Cette categorie existe deja'
                ]);
            }

            //creation via le service
            $categorie = $this->categorieService->create($data);
            return response()->json([
                'success' => true,
                'Message' => 'Categorie cree avec succes',
                'data' => $categorie
            ], 201);

        }catch(\Exception $e){
            return response()->json([
                'message' => 'une erreur inattendue est survenue',
                'error' => $e->getMessage()
            ], 500);

        }
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
