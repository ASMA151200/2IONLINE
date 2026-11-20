<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

/**
 * Diagnostic de l'envoi d'e-mails (création de comptes, mot de passe oublié).
 *   mail:test adresse@exemple.com
 * Affiche la configuration réelle, envoie un message de test IMMÉDIATEMENT
 * (sans file d'attente) et montre l'erreur exacte en cas d'échec.
 */
class MailTest extends Command
{
    protected $signature = 'mail:test {destinataire : Adresse qui recevra le message de test}';

    protected $description = 'Teste la configuration d\'envoi d\'e-mails et l\'état de la file d\'attente';

    public function handle(): int
    {
        $mailer = config('mail.default');
        $cfg = config("mail.mailers.$mailer", []);

        $this->line('Transport     : ' . $mailer);
        $this->line('Serveur       : ' . ($cfg['host'] ?? '—') . ':' . ($cfg['port'] ?? '—'));
        $this->line('Expéditeur    : ' . config('mail.from.address'));
        $this->line('File attente  : ' . config('queue.default'));
        $this->line('Lien du site  : ' . config('app.frontend_url'));

        if (Schema::hasTable('jobs')) {
            $this->line('E-mails en attente d\'envoi : ' . DB::table('jobs')->count());
        }
        if (Schema::hasTable('failed_jobs')) {
            $this->line('Envois échoués : ' . DB::table('failed_jobs')->count());
        }

        if (in_array($mailer, ['log', 'array'], true)) {
            $this->warn("Le transport est « $mailer » : les e-mails sont seulement écrits dans les logs, JAMAIS envoyés. Définissez MAIL_MAILER=smtp (et MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD) dans le .env du serveur.");
        }

        if (config('queue.default') === 'sync') {
            $this->warn('QUEUE_CONNECTION=sync : les e-mails sont envoyés pendant la requête (lent si le serveur SMTP est lent). Préférez QUEUE_CONNECTION=database.');
        }

        try {
            Mail::raw('Test d\'envoi 2I Online : si vous lisez ce message, la configuration e-mail fonctionne.', function ($m) {
                $m->to($this->argument('destinataire'))->subject('Test e-mail 2I Online');
            });
            $this->info('Message de test envoyé à ' . $this->argument('destinataire') . '.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Échec de l\'envoi : ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
