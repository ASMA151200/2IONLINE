<?php
// app/Services/AlertService.php

namespace App\Services;

use App\Models\{Alerte, User};
use App\Notifications\AlerteCoursNotification;

class AlertService
{
    public function envoyerAuxInscrits(Alerte $alerte): void
    {
        $apprenants = User::whereHas('inscriptions', fn($q) =>
            $q->where('cours_id', $alerte->cours_id)->where('statut', 'actif')
        )->get();

        $apprenants->each->notify(new AlerteCoursNotification($alerte));

        $alerte->update([
            'envoye_le'       => now(),
            'nb_push_envoyes' => $apprenants->count(),
        ]);
    }
}