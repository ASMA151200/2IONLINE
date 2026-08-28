<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Partenaire;
use App\Models\Progression;
use App\Models\Resultat;
use App\Models\Certificat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Espace du partenaire connecté — lecture seule sur les formations qu'il
 * finance : impossible pour un partenaire de voir/gérer une formation
 * qu'il ne finance pas (vérifié via la table pivot formation_partenaire,
 * pas par simple rôle).
 */
class PartenaireDashboardController extends Controller
{
    /** Formations financées par le partenaire connecté, avec montant/date */
    public function mesFinancements(Request $request): JsonResponse
    {
        $partenaire = $request->user()->partenaire;

        if (!$partenaire) {
            return response()->json(['success' => false, 'message' => 'Profil partenaire introuvable'], 404);
        }

        $formations = $partenaire->formations()->withCount('inscriptions')->get();

        return response()->json(['success' => true, 'data' => $formations]);
    }

    /**
     * Statistiques agrégées sur l'ensemble des formations financées par
     * ce partenaire — montant total investi, étudiants, taux de réussite,
     * certificats délivrés.
     */
    public function stats(Request $request): JsonResponse
    {
        $partenaire = $request->user()->partenaire;

        if (!$partenaire) {
            return response()->json(['success' => false, 'message' => 'Profil partenaire introuvable'], 404);
        }

        $formations = $partenaire->formations;
        $formationIds = $formations->pluck('id');

        $totalInvesti = $formations->sum(fn ($f) => (float) $f->pivot->montant_finance);

        $totalEtudiants = Inscription::whereIn('formation_id', $formationIds)
            ->where('statut', 'actif')
            ->distinct('user_id')
            ->count('user_id');

        $totalTermines = Inscription::whereIn('formation_id', $formationIds)
            ->where('statut', 'termine')
            ->count();

        $totalInscriptions = Inscription::whereIn('formation_id', $formationIds)->count();
        $tauxReussite = $totalInscriptions > 0 ? round(($totalTermines / $totalInscriptions) * 100, 1) : 0;

        $certificatsDelivres = Certificat::whereIn('formation_id', $formationIds)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'totalFormationsFinancees' => $formations->count(),
                'totalInvesti' => $totalInvesti,
                'totalEtudiants' => $totalEtudiants,
                'tauxReussite' => $tauxReussite,
                'certificatsDelivres' => $certificatsDelivres,
            ],
        ]);
    }

    /**
     * Liste des étudiants d'une formation financée + leur progression —
     * refuse l'accès si le partenaire ne finance pas cette formation
     * précise (vérifié via la table pivot, pas juste le rôle).
     */
    public function etudiantsFormation(Request $request, int $formationId): JsonResponse
    {
        $partenaire = $request->user()->partenaire;

        if (!$partenaire || !$partenaire->formations()->where('formations.id', $formationId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne financez pas cette formation.',
            ], 403);
        }

        $inscriptions = Inscription::with('user')
            ->where('formation_id', $formationId)
            ->where('statut', 'actif')
            ->get();

        $data = $inscriptions->map(function ($inscription) use ($formationId) {
            $user = $inscription->user;

            $totalLecons = Progression::where('user_id', $user->id)
                ->whereHas('lecon.module', fn ($q) => $q->where('formation_id', $formationId))
                ->count();

            $leconsTerminees = Progression::where('user_id', $user->id)
                ->where('statut', 'termine')
                ->whereHas('lecon.module', fn ($q) => $q->where('formation_id', $formationId))
                ->count();

            $moyenneResultats = Resultat::where('user_id', $user->id)
                ->whereHas('examen', fn ($q) => $q->where('formation_id', $formationId))
                ->avg('score');

            return [
                'userId' => (string) $user->id,
                'prenom' => $user->prenom,
                'nom' => $user->nom,
                'email' => $user->email,
                'progression' => $totalLecons > 0 ? round(($leconsTerminees / $totalLecons) * 100) : 0,
                'moyenneExamens' => $moyenneResultats ? round((float) $moyenneResultats, 1) : null,
                'statutInscription' => $inscription->statut,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }
}
