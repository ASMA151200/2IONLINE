<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEtudiantRequest;
use App\Models\Etudiant;
use App\Services\EtudiantService;
use Illuminate\Http\Request;

class EtudiantController extends Controller
{
    public function __construct( protected EtudiantService $etudiantService)
    {
        $this->etudiantService = $etudiantService;
    }

    /**
     * Liste des etudiants
     */
    public function index()
    {
        $etudiant = $this->etudiantService->getAll();
        return response()->json([
            'success' => true,
            'data' => $etudiant
            ], 200);
    }

    /**
     * creation d etudiant
     */
    public function store(StoreEtudiantRequest $request,)
    {
        $data = $this->etudiantService->store($request->validated());
        return response()->json([
            'success'             => true,
            'message'             => 'Etudiant créé avec succès',
            'password_temporaire' => $data['password'],
            'data'                => $data['etudiant'],
            ], 201);

    }

    /**
     * Afficher un etudiant
     */
    public function show(Etudiant $etudiant)
    {
        $etudiant = $this->etudiantService->show($etudiant);
        return response()->json([
            'success' => true,
            'data' => $etudiant
                ], 200);
    }

    /**
     * Update etudiant
     */
    public function update(StoreEtudiantRequest $request, Etudiant $etudiant)
    {
        $etudiant = $this->etudiantService->update($etudiant, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Etudiant mis à jour avec succès',
            'data'    => $etudiant,
        ], 200);
    }

    /**
     * supprimer etudiant
     */
    public function destroy(Etudiant $etudiant)
    {
        $this->etudiantService->delete($etudiant);
        return response()->json([
            'success' => true,
            'message' => 'Etudiant supprimé avec succès',
        ], 200);
    }
    
}
