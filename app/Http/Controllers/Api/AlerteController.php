<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerte;
use App\Models\Inscription;
use App\Models\User;
use App\Notifications\AlerteNotification;
use App\Traits\ChecksFormationOwnership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    use ChecksFormationOwnership;

    /**
     * Liste des alertes (filtrable par formation_id) — accès identique
     * aux autres contenus de formation (propriétaire ou inscrit actif).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Alerte::with(['formation', 'formateur'])->latest();

        if ($request->filled('formation_id')) {
            $this->authorizeFormationAccess($request->input('formation_id'));
            $query->where('formation_id', $request->input('formation_id'));
        } else {
            $this->scopeToAccessibleFormations($query);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    /**
     * Crée une alerte et l'envoie immédiatement en notification push à
     * tous les étudiants activement inscrits à la formation concernée.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'live_session_id' => 'nullable|exists:live_sessions,id',
            'type' => 'required|in:rappel_live,annulation,deadline,annonce',
            'titre' => 'required|string|max:100',
            'message' => 'required|string|max:300',
        ]);

        $this->authorizeFormationOwner($data['formation_id']);

        $data['formateur_id'] = $request->user()->id;
        $alerte = Alerte::create($data);

        // Envoi réel aux étudiants inscrits activement à cette formation
        $studentIds = Inscription::where('formation_id', $data['formation_id'])
            ->where('statut', 'actif')
            ->pluck('user_id');

        $students = User::whereIn('id', $studentIds)->get();
        $nbEnvoyes = 0;

        foreach ($students as $student) {
            if ($student->pushSubscriptions()->exists()) {
                $student->notify(new AlerteNotification($alerte));
                $nbEnvoyes++;
            }
        }

        $alerte->update(['envoye_le' => now(), 'nb_push_envoyes' => $nbEnvoyes]);

        return response()->json([
            'success' => true,
            'message' => "Alerte créée et envoyée à {$nbEnvoyes} étudiant(s) abonné(s) aux notifications.",
            'data' => $alerte->fresh(),
        ], 201);
    }
}
