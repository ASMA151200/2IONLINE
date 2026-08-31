<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        Message::query()->delete();

        $awa = User::where('email', 'awa.fall@example.com')->first();
        $aminata = User::where('email', 'aminata.diallo@2i-online.com')->first();
        $ibrahima = User::where('email', 'ibrahima.sow@example.com')->first();
        $moussa = User::where('email', 'moussa.ndiaye@2i-online.com')->first();

        if (!$awa || !$aminata) {
            $this->command->error('Utilisateurs de démonstration introuvables — lancez FormateurSeeder et EtudiantSeeder avant MessageSeeder.');
            return;
        }

        // Conversation 1 : Awa <-> Chef Aminata
        $conversation1 = [
            ['from' => $awa, 'to' => $aminata, 'content' => "Bonjour Chef, désolée pour mon absence de mardi, j'étais souffrante. Est-ce que je peux rattraper l'atelier manqué ?", 'read' => true],
            ['from' => $aminata, 'to' => $awa, 'content' => "Bonjour Awa, j'espère que tu vas mieux ! Oui bien sûr, passe me voir avant le prochain cours qu'on trouve un créneau ensemble.", 'read' => true],
            ['from' => $awa, 'to' => $aminata, 'content' => "Merci beaucoup Chef, je vous tiens au courant dès que je suis rétablie.", 'read' => false],
        ];

        // Conversation 2 : Ibrahima <-> Chef Moussa
        $conversation2 = [
            ['from' => $ibrahima, 'to' => $moussa, 'content' => "Bonjour Chef Moussa, avez-vous une recommandation de livre pour approfondir les bases de la viennoiserie en dehors des cours ?", 'read' => true],
            ['from' => $moussa, 'to' => $ibrahima, 'content' => "Bonjour Ibrahima, je te recommande de te concentrer d'abord sur la pratique qu'on voit en atelier — la théorie viendra naturellement. Mais si tu veux vraiment un livre, \"Le Grand Manuel du Pâtissier\" est une bonne base.", 'read' => false],
        ];

        foreach ([$conversation1, $conversation2] as $conversation) {
            foreach ($conversation as $i => $msg) {
                Message::create([
                    'sender_id' => $msg['from']->id,
                    'receiver_id' => $msg['to']->id,
                    'content' => $msg['content'],
                    'read_at' => $msg['read'] ? now()->subHours(count($conversation) - $i) : null,
                    'created_at' => now()->subHours(count($conversation) - $i + 1),
                ]);
            }
        }

        $this->command->info('2 conversations de démonstration créées avec succès.');
    }
}
