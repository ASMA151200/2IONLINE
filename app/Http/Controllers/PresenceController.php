<?php

namespace App\Http\Controllers;

use App\Models\LiveSession;
use App\Models\Presence;
use App\Traits\ChecksFormationOwnership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    use ChecksFormationOwnership;

    /**
     * Marquer les présences d'une session live (formateur/admin,
     * propriétaire de la formation) — body: { presences: [{user_id, present}] }
     */
    public function marquer(Request $request, LiveSession $liveSession): JsonResponse
    {
        $this->authorizeFormationOwner($liveSession->formation_id);

        $data = $request->validate([
            'presences' => 'required|array|min:1',
            'presences.*.user_id' => 'required|exists:users,id',
            'presences.*.present' => 'required|boolean',
        ]);

        foreach ($data['presences'] as $p) {
            Presence::updateOrCreate(
                ['live_session_id' => $liveSession->id, 'user_id' => $p['user_id']],
                ['present' => $p['present'], 'marked_at' => now()],
            );
        }

        return response()->json(['success' => true, 'message' => 'Présences enregistrées avec succès']);
    }

    /** Liste des présences d'une session (formateur/admin propriétaire) */
    public function index(Request $request, LiveSession $liveSession): JsonResponse
    {
        $this->authorizeFormationAccess($liveSession->formation_id);

        return response()->json([
            'success' => true,
            'data' => Presence::with('user')->where('live_session_id', $liveSession->id)->get(),
        ]);
    }

    /** Historique de présence de l'utilisateur connecté */
    public function mesPresences(Request $request): JsonResponse
    {
        $presences = Presence::with('liveSession.formation')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json(['success' => true, 'data' => $presences]);
    }
}
