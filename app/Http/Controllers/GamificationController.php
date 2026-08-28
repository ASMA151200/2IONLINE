<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GamificationController extends Controller
{
    /** Liste de tous les badges existants */
    public function badges(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => Badge::all()]);
    }

    /** Mes badges + mes points (étudiant connecté) */
    public function mesBadges(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'points' => $user->points,
                'badges' => $user->badges()->get(),
            ],
        ]);
    }

    /** Classement des étudiants par points (top 20, public aux connectés) */
    public function classement(): JsonResponse
    {
        $top = User::where('role', 'etudiant')
            ->orderByDesc('points')
            ->limit(20)
            ->get(['id', 'prenom', 'nom', 'points']);

        return response()->json(['success' => true, 'data' => $top]);
    }

    /**
     * Attribue un badge à un utilisateur (admin/formateur uniquement) —
     * ajoute aussi les points associés au badge.
     */
    public function attribuer(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'badge_id' => 'required|exists:badges,id',
        ]);

        $user = User::findOrFail($data['user_id']);
        $badge = Badge::findOrFail($data['badge_id']);

        if ($user->badges()->where('badge_id', $badge->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Ce badge est déjà attribué.'], 422);
        }

        $user->badges()->attach($badge->id, ['obtenu_le' => now()]);
        $user->increment('points', $badge->points);

        return response()->json(['success' => true, 'message' => 'Badge attribué avec succès']);
    }
}
