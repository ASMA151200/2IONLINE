<?php

namespace App\Console\Commands;

use App\Models\Inscription;
use Illuminate\Console\Command;

/**
 * Repère les apprenants qui suivent plusieurs formations en même temps
 * (plusieurs inscriptions « actif »), alors qu'une seule est permise.
 *
 *   inscriptions:doublons             → liste seulement (aucune modification)
 *   inscriptions:doublons --corriger  → garde la PLUS RÉCENTE, passe les autres à « annulé »
 */
class InscriptionsDoublons extends Command
{
    protected $signature = 'inscriptions:doublons {--corriger : Annule les inscriptions actives les plus anciennes}';

    protected $description = 'Liste (ou corrige) les apprenants inscrits à plusieurs formations actives';

    public function handle(): int
    {
        $userIds = Inscription::where('statut', 'actif')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_id');

        if ($userIds->isEmpty()) {
            $this->info('Aucun apprenant en double : tout est conforme.');
            return self::SUCCESS;
        }

        foreach ($userIds as $userId) {
            $actives = Inscription::with(['user:id,prenom,nom,email', 'formation:id,titre'])
                ->where('user_id', $userId)->where('statut', 'actif')
                ->orderByDesc('date')->orderByDesc('id')->get();

            $u = $actives->first()->user;
            $this->line("<fg=yellow>{$u->prenom} {$u->nom}</> ({$u->email})");

            foreach ($actives as $i => $ins) {
                $marque = $i === 0 ? 'GARDÉE ' : 'ancienne';
                $this->line("   [{$marque}] #{$ins->id} {$ins->formation->titre} — depuis le {$ins->date}");
            }

            if ($this->option('corriger')) {
                Inscription::whereIn('id', $actives->slice(1)->pluck('id'))->update(['statut' => 'annule']);
                $this->info('   → anciennes inscriptions annulées.');
            }
        }

        $this->newLine();
        $this->info($userIds->count() . ' apprenant(s) concerné(s)' . ($this->option('corriger') ? ' — corrigé(s).' : ' — relancez avec --corriger pour régulariser.'));

        return self::SUCCESS;
    }
}
