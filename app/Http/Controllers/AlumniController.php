<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlumniController extends Controller
{
    /**
     * Annuaire public des alumni ayant explicitement choisi d'être
     * visibles (alumni_visible = true) — opt-in, jamais automatique,
     * pour respecter la vie privée des anciens élèves.
     */
    public function index(): JsonResponse
    {
        $alumni = Etudiant::with('user', 'formations')
            ->where('alumni_visible', true)
            ->get()
            ->map(fn ($e) => [
                'id' => $e->id,
                'prenom' => $e->user->prenom,
                'nom' => $e->user->nom,
                'posteActuel' => $e->poste_actuel,
                'entrepriseActuelle' => $e->entreprise_actuelle,
                'formations' => $e->formations->pluck('titre'),
            ]);

        return response()->json(['success' => true, 'data' => $alumni]);
    }

    /** L'étudiant connecté active/désactive sa visibilité dans l'annuaire */
    public function updateVisibilite(Request $request): JsonResponse
    {
        $data = $request->validate([
            'alumni_visible' => 'required|boolean',
            'poste_actuel' => 'nullable|string|max:255',
            'entreprise_actuelle' => 'nullable|string|max:255',
        ]);

        $etudiant = $request->user()->etudiant;

        if (!$etudiant) {
            return response()->json(['success' => false, 'message' => 'Profil étudiant introuvable'], 404);
        }

        $etudiant->update($data);

        return response()->json(['success' => true, 'message' => 'Visibilité alumni mise à jour', 'data' => $etudiant]);
    }
}
