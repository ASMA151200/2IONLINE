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

Artisan::command('push:test {target?}', function (?string $target = null) {
    // Accepte soit un ID numérique, soit un email directement — par
    // défaut binetaseck.incubinstitut@gmail.com plutôt que le premier
    // utilisateur de la base (souvent un compte admin de test sans
    // rapport avec ce qu'on veut réellement vérifier).
    $target = $target ?? 'binetaseck.incubinstitut@gmail.com';

    $user = is_numeric($target)
        ? User::find((int) $target)
        : User::where('email', $target)->first();

    if (! $user) {
        $this->error("Aucun utilisateur trouvé pour « {$target} ».");
        return;
    }

    // Sans ça, un "envoyé avec succès" peut être trompeur : la
    // notification part bien du serveur, mais n'a littéralement nulle
    // part où aller si l'utilisateur n'a jamais autorisé les
    // notifications sur un navigateur (bouton cloche jamais cliqué).
    if ($user->pushSubscriptions()->count() === 0) {
        $this->warn("⚠ {$user->email} n'a aucun abonnement push actif — il doit d'abord activer les notifications depuis l'interface (bouton cloche) sur un navigateur, sinon ce test n'aura aucun effet visible.");
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
