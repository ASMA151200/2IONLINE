<?php

namespace App\Mail;

use App\Models\Etudiant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EtudiantCreeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Etudiant $etudiant,
        public string $password
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenue chez 2i Online – Votre première leçon vous attend !',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mailEtudiant',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
