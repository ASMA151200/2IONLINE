<?php

namespace App\Http\Controllers;

use App\Models\Partenaire;
use App\Http\Requests\StorePartenaireRequest;
use App\Http\Requests\UpdatePartenaireRequest;
use App\Services\PartenaireService;
use Illuminate\Http\Request;

class PartenaireController extends Controller
{
    public function __construct(protected PartenaireService $partenaireService)
    {
    }

    /**
     * Liste des partenaires (admin uniquement)
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => $this->partenaireService->getAll()
        ]);
    }

    /**
     * Creer un partenaire (admin uniquement)
     */
    public function store(StorePartenaireRequest $request)
    {
        $data = $this->partenaireService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Partenaire créé avec succès',
            'password_temporaire' => $data['password'],
            'data' => $data['partenaire']
        ], 201);
    }

    /**
     * Afficher un partenaire
     */
    public function show(Partenaire $partenaire)
    {
        return response()->json([
            'success' => true,
            'data' => $partenaire->load(['user', 'formations'])
        ]);
    }

    /**
     * Modifier un partenaire
     */
    public function update(UpdatePartenaireRequest $request, Partenaire $partenaire)
    {
        $partenaire = $this->partenaireService->update($partenaire, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Partenaire modifié avec succès',
            'data' => $partenaire
        ], 201);
    }

    /**
     * Supprimer un partenaire
     */
    public function destroy(Partenaire $partenaire)
    {
        $this->partenaireService->destroy($partenaire);

        return response()->json([
            'success' => true,
            'message' => 'Partenaire supprimé avec succès'
        ], 201);
    }

    /**
     * Activer / désactiver le compte (admin uniquement)
     */
    public function toggleActive(Partenaire $partenaire)
    {
        $user = $this->partenaireService->toggleActive($partenaire);

        return response()->json([
            'success' => true,
            'message' => $user->is_active ? 'Compte activé' : 'Compte désactivé',
            'data' => ['is_active' => $user->is_active],
        ]);
    }

    /**
     * Réinitialiser le mot de passe (admin uniquement)
     */
    public function resetPassword(Partenaire $partenaire)
    {
        $password = $this->partenaireService->resetPassword($partenaire);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe réinitialisé et envoyé par email',
            'password_temporaire' => $password,
        ]);
    }

    /**
     * Financer une formation (admin uniquement) — attache/actualise le
     * lien formation<->partenaire avec un montant et une date.
     * POST /v1/partenaires/{partenaire}/financer
     */
    public function financer(Request $request, Partenaire $partenaire)
    {
        $data = $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'montant_finance' => 'required|numeric|min:0',
            'date_financement' => 'required|date',
        ]);

        $this->partenaireService->financerFormation(
            $partenaire,
            $data['formation_id'],
            $data['montant_finance'],
            $data['date_financement'],
        );

        return response()->json([
            'success' => true,
            'message' => 'Financement enregistré avec succès',
            'data' => $partenaire->load('formations'),
        ], 201);
    }

    /**
     * Retirer un financement (admin uniquement)
     * DELETE /v1/partenaires/{partenaire}/financer/{formation}
     */
    public function retirerFinancement(Partenaire $partenaire, int $formation)
    {
        $this->partenaireService->retirerFinancement($partenaire, $formation);

        return response()->json([
            'success' => true,
            'message' => 'Financement retiré avec succès',
        ]);
    }
}
