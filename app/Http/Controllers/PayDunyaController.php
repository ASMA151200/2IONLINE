<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Paiement;
use App\Services\PayDunyaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayDunyaController extends Controller
{
    public function __construct(protected PayDunyaService $payDunya)
    {
    }

    /**
     * POST /v1/paiements/{paiement}/paydunya  (protégée, auth:sanctum)
     * Crée l'invoice PayDunya pour un paiement déjà créé (POST /v1/paiements,
     * statut "en attente") et renvoie l'URL de paiement hébergée.
     */
    public function initiate(Request $request, Paiement $paiement)
    {
        if ($paiement->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        if ($paiement->statut === 'confirme') {
            return response()->json(['success' => false, 'message' => 'Ce paiement est déjà confirmé'], 422);
        }

        $paiement->load('formation');
        $frontendUrl = rtrim(config('app.frontend_url', 'https://www.2i-online.com'), '/');
        $user = $request->user();

        try {
            $invoice = $this->payDunya->createInvoice([
                'total_amount' => (int) round($paiement->montant),
                'description' => 'Inscription — ' . ($paiement->formation->titre ?? 'Formation'),
                'store_name' => '2I Online',
                'callback_url' => route('paydunya.ipn'),
                'return_url' => "{$frontendUrl}/payment/success?paiement_id={$paiement->id}",
                'cancel_url' => "{$frontendUrl}/payment/error?paiement_id={$paiement->id}",
                'custom_data' => ['paiement_id' => $paiement->id],
                'customer_name' => trim(($user->prenom ?? '') . ' ' . ($user->nom ?? '')),
                'customer_email' => $user->email,
                'customer_phone' => $user->telephone ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('[PayDunya] Échec initiation', ['error' => $e->getMessage(), 'paiement_id' => $paiement->id]);
            return response()->json(['success' => false, 'message' => "Erreur lors de l'initialisation du paiement"], 502);
        }

        $paiement->update(['paydunya_token' => $invoice['token']]);

        return response()->json([
            'success' => true,
            'data' => ['checkout_url' => $invoice['checkout_url']],
        ]);
    }

    /**
     * POST /v1/webhooks/paydunya  (PUBLIQUE — hors auth:sanctum)
     * IPN PayDunya : appelée directement par leurs serveurs, en
     * application/x-www-form-urlencoded, avec les données sous la clé
     * "data" et un hash (SHA-512 de la clé maître) pour vérifier
     * l'authenticité.
     */
    public function ipn(Request $request)
    {
        $data = $request->input('data');

        if (!is_array($data)) {
            // PayDunya envoie parfois "data" en JSON string plutôt qu'en
            // tableau imbriqué selon le client HTTP utilisé côté client.
            $decoded = json_decode($request->input('data', ''), true);
            $data = is_array($decoded) ? $decoded : null;
        }

        if (!$data) {
            Log::warning('[PayDunya IPN] Payload invalide ou vide', ['raw' => $request->all()]);
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $hash = $data['hash'] ?? null;
        if (!$this->payDunya->verifyHash($hash)) {
            Log::warning('[PayDunya IPN] Hash invalide — requête rejetée');
            return response()->json(['message' => 'Invalid hash'], 401);
        }

        $token = $data['invoice']['token'] ?? $data['token'] ?? null;
        $status = strtolower($data['status'] ?? $data['invoice']['status'] ?? '');

        if (!$token) {
            return response()->json(['message' => 'Missing token'], 400);
        }

        $paiement = Paiement::where('paydunya_token', $token)->first();

        // Le même webhook IPN gère aussi les dons ponctuels (DonController)
        // — un token PayDunya donné ne correspond jamais qu'à un seul des
        // deux (Paiement OU Don), jamais les deux à la fois.
        if (!$paiement) {
            $don = \App\Models\Don::where('paydunya_token', $token)->first();

            if ($don) {
                if ($don->statut === 'confirme') {
                    return response()->json(['success' => true]);
                }
                if ($status === 'completed') {
                    $don->update(['statut' => 'confirme']);
                } elseif (in_array($status, ['cancelled', 'canceled', 'failed', 'fail'], true)) {
                    $don->update(['statut' => 'echec']);
                }
                return response()->json(['success' => true]);
            }

            Log::warning('[PayDunya IPN] Paiement/Don introuvable pour ce token', ['token' => $token]);
            return response()->json(['message' => 'Paiement introuvable'], 404);
        }

        // Idempotence : si déjà confirmé, on répond 200 sans rien refaire.
        if ($paiement->statut === 'confirme') {
            return response()->json(['success' => true]);
        }

        if ($status === 'completed') {
            $paiement->update(['statut' => 'confirme']);

            // Active (ou crée) l'inscription correspondante — c'est ici,
            // et UNIQUEMENT ici, qu'un paiement confirmé donne accès à la
            // formation.
            Inscription::updateOrCreate(
                [
                    'user_id' => $paiement->user_id,
                    'formation_id' => $paiement->formation_id,
                ],
                [
                    'date' => now()->toDateString(),
                    'statut' => 'actif',
                ]
            );
        } elseif (in_array($status, ['cancelled', 'canceled', 'failed', 'fail'], true)) {
            $paiement->update(['statut' => 'echec']);
        }
        // Autre statut (pending...) : on attend une prochaine notification
        // avec un statut final.

        return response()->json(['success' => true]);
    }
}
