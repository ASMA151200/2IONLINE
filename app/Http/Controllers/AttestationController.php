<?php

namespace App\Http\Controllers;

use App\Models\Attestation;
use App\Models\LiveSession;
use App\Models\Presence;
use App\Models\User;
use App\Traits\ChecksFormationOwnership;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * NÉCESSITE barryvdh/laravel-dompdf (déjà requis pour les certificats de
 * réussite, voir CertificatGeneratorService) — même dépendance réutilisée.
 */
class AttestationController extends Controller
{
    use ChecksFormationOwnership;

    /** Génère une attestation pour chaque étudiant marqué présent (formateur/admin) */
    public function generer(Request $request, LiveSession $liveSession): JsonResponse
    {
        $this->authorizeFormationOwner($liveSession->formation_id);

        $presents = Presence::with('user')
            ->where('live_session_id', $liveSession->id)
            ->where('present', true)
            ->get();

        $attestations = [];

        foreach ($presents as $presence) {
            $user = $presence->user;
            $numero = 'ATT-' . now()->format('Y') . '-' . strtoupper(Str::random(8));

            $pdf = app('dompdf.wrapper')->loadView('certificats.attestation', [
                'user' => $user,
                'liveSession' => $liveSession,
                'numero' => $numero,
                'date' => now()->format('d/m/Y'),
            ])->setPaper('a4', 'landscape');

            $path = 'attestations/' . $numero . '.pdf';
            Storage::disk('public')->put($path, $pdf->output());

            $attestations[] = Attestation::updateOrCreate(
                ['user_id' => $user->id, 'live_session_id' => $liveSession->id],
                ['numero_attestation' => $numero, 'fichier_pdf' => $path, 'date_delivrance' => now()->toDateString()],
            );
        }

        return response()->json([
            'success' => true,
            'message' => count($attestations) . ' attestation(s) générée(s) avec succès',
            'data' => $attestations,
        ], 201);
    }

    /** Mes attestations (étudiant connecté) */
    public function mesAttestations(Request $request): JsonResponse
    {
        $attestations = Attestation::with('liveSession.formation')
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(fn ($a) => [
                ...$a->toArray(),
                'fichier_pdf_url' => Storage::disk('public')->url($a->fichier_pdf),
            ]);

        return response()->json(['success' => true, 'data' => $attestations]);
    }
}
