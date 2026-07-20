<?php
// app/Http/Controllers/Api/PushSubscriptionController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'endpoint'    => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth'   => 'required|string',
        ]);

        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $user->updatePushSubscription(
            $request->endpoint,
            $request->input('keys.p256dh'),
            $request->input('keys.auth')
        );

        return response()->json(['message' => 'Abonnement enregistré']);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        $user->deletePushSubscription($request->endpoint);

        return response()->json(['message' => 'Désabonné']);
    }
}