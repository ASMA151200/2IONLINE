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
        return new Content(
            view: 'emails.mailPartenaire',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
