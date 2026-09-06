<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Commande de secours : l'environnement d'exécution disponible ne
 * permet QUE de lancer "php artisan <commande>" (pas de vrai terminal
 * shell — tail/find/ls/pwd échouent tous car ils sont interprétés comme
 * "php artisan tail" etc.). Cette commande affiche le contenu du log
 * Laravel via ce même mécanisme, sans avoir besoin d'un accès shell.
 *
 * Usage : php artisan logs:tail
 *         php artisan logs:tail --lines=200
 */
class TailLogs extends Command
{
    protected $signature = 'logs:tail {--lines=100 : Nombre de lignes à afficher, en partant de la fin}';

    protected $description = 'Affiche les dernières lignes de storage/logs/laravel.log';

    public function handle(): int
    {
        $path = storage_path('logs/laravel.log');

        if (!file_exists($path)) {
            $this->error("Le fichier n'existe pas : {$path}");

            // Aide au diagnostic : liste tout ce qui existe réellement
            // dans storage/logs, au cas où le nom de fichier serait
            // différent (rotation quotidienne, canal autre que "single").
            $logsDir = storage_path('logs');
            if (is_dir($logsDir)) {
                $this->line('Fichiers présents dans storage/logs :');
                foreach (scandir($logsDir) as $f) {
                    if ($f !== '.' && $f !== '..') {
                        $this->line(' - ' . $f);
                    }
                }
            } else {
                $this->error("Le dossier storage/logs n'existe pas non plus : {$logsDir}");
            }

            return self::FAILURE;
        }

        $lines = (int) $this->option('lines');
        $content = file($path);
        $tail = array_slice($content, -$lines);

        $this->line(implode('', $tail));

        return self::SUCCESS;
    }
}
