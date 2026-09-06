<?php

use App\Models\Alerte;
use App\Models\Formation;
use App\Models\User;
use App\Notifications\AlerteNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('push:test {userId?}', function (?int $userId = null) {
    $user = $userId ? User::find($userId) : User::first();

    if (! $user) {
        $this->error('Aucun utilisateur trouvé.');
        return;
    }

    // ATTENTION: utilisait auparavant 'cours_id' (champ inexistant,
    // Alerte utilise 'formation_id' depuis la correction du modèle) et
    // AlerteCoursNotification (classe dupliquée, cassée pour la même
    // raison, supprimée) — cette commande n'avait donc jamais pu
    // fonctionner. Corrigée pour utiliser une vraie formation existante
    // et la classe de notification réellement utilisée en production
    // (AlerteNotification, celle branchée sur AlerteController).
    $formation = Formation::first();

    if (! $formation) {
        $this->error("Aucune formation trouvée — impossible de tester sans formation_id valide.");
        return;
    }

    $alerte = new Alerte([
        'titre' => 'Test push',
        'message' => 'Ceci est une notification de test.',
        'formation_id' => $formation->id,
        'type' => 'annonce',
        'formateur_id' => $user->id,
    ]);

    $user->notify(new AlerteNotification($alerte));

    $this->info('Notification push de test envoyée à ' . $user->email);
})->purpose('Envoyer une notification push de test à un utilisateur');

Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()
    ->withoutOverlapping();
