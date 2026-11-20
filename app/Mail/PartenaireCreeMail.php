<?php

namespace App\Mail;

use App\Models\Partenaire;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartenaireCreeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Partenaire $partenaire,
        public string $password
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue sur 2IOnline - Vos identifiants de connexion',
        );
    }

    public function content(): Content
    {
        // Les gabarits attendent ces liens ; sans eux ils valaient tous "#" et
        // ne menaient nulle part. Le bouton principal pointe vers la connexion :
        // l'utilisateur y entre l'e-mail et le mot de passe temporaire reçus.
        $site = rtrim((string) config('app.frontend_url', 'https://www.2i-online.com'), '/');

        return new Content(
            view: 'emails.mailPartenaire',
            with: [
                'lien_plateforme' => $site,
                'lien_espace_partenaire' => $site . '/login',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
