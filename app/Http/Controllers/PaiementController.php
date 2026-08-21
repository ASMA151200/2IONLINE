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

        // SÉCURITÉ: un paiement créé ici (avant redirection vers PayDunya)
        // ne doit JAMAIS être marqué "confirme" immédiatement — c'est
        // uniquement PayDunyaController::ipn() (webhook vérifié) qui peut
        // le faire, après confirmation réelle du paiement. Sans ce
        // changement, n'importe quel appel à cette route créait un
        // paiement "confirmé" sans qu'aucun argent n'ait réellement changé
        // de main.
        $data['statut'] = 'en attente';

        $paiement = $this->paiementService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Paiement enregistré, en attente de confirmation',
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