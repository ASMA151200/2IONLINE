<?php

use App\Models\Alerte;
use App\Models\User;
use App\Notifications\AlerteCoursNotification;
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

    $alerte = new Alerte([
        'titre' => 'Test push',
        'message' => 'Ceci est une notification de test.',
        'cours_id' => 1,
        'type' => 'annonce',
        'formateur_id' => $user->id,
    ]);

    $user->notify(new AlerteCoursNotification($alerte));

    $this->info('Notification push de test envoyée à ' . $user->email);
})->purpose('Envoyer une notification push de test à un utilisateur');

Schedule::command('queue:work --stop-when-empty --max-time=50')
    ->everyMinute()
    ->withoutOverlapping();
