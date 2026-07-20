<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Http\Requests\StorePaiementRequest;
use App\Http\Requests\UpdatePaiementRequest;
use App\Services\PaiementService;

class PaiementController extends Controller
{
    public function __construct(
        protected PaiementService $paiementService
    ) {}

    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->paiementService->getAll()
        ]);
    }

    public function store(StorePaiementRequest $request)
    {
        $data = $request->validated();

        // utilisateur connecté
        $data['user_id'] = auth()->id();

        // statut par défaut
        $data['statut'] = 'confirme';

        $paiement = $this->paiementService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Paiement effectué avec succès',
            'data' => $paiement->load(['user', 'formation'])
        ], 201);
    }

    public function show(Paiement $paiement)
    {
        return response()->json([
            'success' => true,
            'data' => $paiement->load(['user', 'formation'])
        ]);
    }

    public function update(UpdatePaiementRequest $request, Paiement $paiement)
    {
        $paiement = $this->paiementService->update(
            $paiement,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Paiement mis à jour',
            'data' => $paiement
        ]);
    }

    public function destroy(Paiement $paiement)
    {
        $this->paiementService->delete($paiement);

        return response()->json([
            'success' => true,
            'message' => 'Paiement supprimé'
        ]);
    }
}