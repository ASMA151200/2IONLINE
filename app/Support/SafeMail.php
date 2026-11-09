<?php

namespace App\Support;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Envoi d'e-mail qui ne peut JAMAIS bloquer ni faire échouer l'action
 * appelante (création/réinitialisation d'un compte).
 *
 * Avant : Mail::send() synchrone dans la transaction de création. Un serveur
 * SMTP lent ou injoignable faisait traîner la requête jusqu'au délai maximal
 * (504, page qui ne charge pas) ou annulait la création du compte.
 * Maintenant : le message est mis en file d'attente (le worker tourne chaque
 * minute) et toute erreur est seulement journalisée. Le mot de passe
 * temporaire reste de toute façon renvoyé à l'administrateur par l'API.
 */
class SafeMail
{
    public static function queue(string $to, Mailable $mailable): void
    {
        try {
            Mail::to($to)->queue($mailable);
        } catch (\Throwable $e) {
            Log::warning('Envoi d\'e-mail impossible (action non bloquée)', [
                'to' => $to,
                'mailable' => get_class($mailable),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
