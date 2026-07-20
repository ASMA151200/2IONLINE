<?php
// app/Jobs/RappelLiveJob.php

namespace App\Jobs;

use App\Models\{Alerte, LiveSession};
use App\Services\AlertService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class RappelLiveJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(private LiveSession $session) {}

    public function handle(AlertService $alertService): void
    {
        $dejaEnvoye = Alerte::where('live_session_id', $this->session->id)
            ->where('type', 'rappel_live')
            ->exists();

        if ($dejaEnvoye) return;

        $alerte = Alerte::create([
            'cours_id'        => $this->session->cours_id,
            'formateur_id'    => $this->session->formateur_id,
            'live_session_id' => $this->session->id,
            'type'            => 'rappel_live',
            'titre'           => 'Le cours commence bientôt',
            'message'         => "\"{$this->session->titre}\" commence dans 30 minutes.",
        ]);

        $alertService->envoyerAuxInscrits($alerte);
    }
}