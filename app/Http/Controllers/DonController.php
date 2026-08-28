<?php

namespace App\Http\Controllers;

use App\Models\Don;
use App\Services\PayDunyaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Dons ponctuels — flux volontairement PUBLIC (pas d'auth:sanctum
 * requise) : un visiteur non connecté doit pouvoir faire un don. Si
 * l'utilisateur est connecté, son user_id est quand même rattaché pour
 * historique ; sinon nom/email fournis manuellement (don "anonyme" côté
 * plateforme, mais PayDunya connaît toujours l'identité réelle du
 * payeur).
 */
class DonController extends Controller
{
    public function __construct(protected PayDunyaService $payDunya)
    {
    }

    /** POST /v1/dons — crée le don ET initie le paiement PayDunya en un seul appel */
    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'montant' => 'required|numeric|min:500',
            'nom_donateur' => 'required_without:user_id|string|max:255',
            'email_donateur' => 'nullable|email',
            'message' => 'nullable|string|max:500',
        ]);

        $user = $request->user(); // null si non connecté (route publique)

        $don = Don::create([
            'user_id' => $user?->id,
            'nom_donateur' => $data['nom_donateur'] ?? trim(($user->prenom ?? '') . ' ' . ($user->nom ?? '')),
            'email_donateur' => $data['email_donateur'] ?? $user?->email,
            'montant' => $data['montant'],
            'message' => $data['message'] ?? null,
            'statut' => 'en attente',
        ]);

        $frontendUrl = rtrim(config('app.frontend_url', 'https://www.2i-online.com'), '/');

        try {
            $invoice = $this->payDunya->createInvoice([
                'total_amount' => (int) round($don->montant),
                'description' => 'Don à 2I Online',
                'store_name' => '2I Online',
                'callback_url' => route('paydunya.ipn'),
                'return_url' => "{$frontendUrl}/don/success?don_id={$don->id}",
                'cancel_url' => "{$frontendUrl}/don/error?don_id={$don->id}",
                'custom_data' => ['type' => 'don', 'don_id' => $don->id],
                'customer_name' => $don->nom_donateur,
                'customer_email' => $don->email_donateur,
            ]);
        } catch (\Throwable $e) {
            Log::error('[PayDunya] Échec initiation don', ['error' => $e->getMessage(), 'don_id' => $don->id]);
            return response()->json(['success' => false, 'message' => "Erreur lors de l'initialisation du don"], 502);
        }

        $don->update(['paydunya_token' => $invoice['token']]);

        return response()->json(['success' => true, 'data' => ['checkout_url' => $invoice['checkout_url']]]);
    }

    /** Total des dons confirmés (affichage public, page Impact) */
    public function total(): JsonResponse
    {
        $total = Don::where('statut', 'confirme')->sum('montant');
        $nombre = Don::where('statut', 'confirme')->count();

        return response()->json(['success' => true, 'data' => ['total' => (float) $total, 'nombre' => $nombre]]);
    }
}
