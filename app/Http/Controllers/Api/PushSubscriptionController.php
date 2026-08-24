<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Enregistre l'abonnement push du navigateur de l'utilisateur connecté
     * (payload standard de l'API PushSubscription du navigateur).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $request->user()->updatePushSubscription(
            endpoint: $data['endpoint'],
            publicKey: $data['keys']['p256dh'],
            authToken: $data['keys']['auth'],
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifications activées avec succès.',
        ], 201);
    }

    /**
     * Désabonne le navigateur courant des notifications push.
     */
    public function destroy(Request $request): JsonResponse
    {
        $endpoint = $request->input('endpoint');

        if ($endpoint) {
            $request->user()->deletePushSubscription($endpoint);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifications désactivées.',
        ]);
    }
}
