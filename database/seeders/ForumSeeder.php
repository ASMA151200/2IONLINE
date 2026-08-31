<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\ForumPost;
use App\Models\ForumReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        ForumPost::query()->delete(); // supprime aussi les réponses en cascade

        $cuisine = Formation::where('titre', 'CAP Cuisinier')->first();
        $patisserie = Formation::where('titre', 'CAP Pâtissier')->first();
        $serveur = Formation::where('titre', 'CAP Serveur')->first();

        $awa = User::where('email', 'awa.fall@example.com')->first();
        $ibrahima = User::where('email', 'ibrahima.sow@example.com')->first();
        $mariama = User::where('email', 'mariama.ba@example.com')->first();
        $aminata = User::where('email', 'aminata.diallo@2i-online.com')->first();
        $moussa = User::where('email', 'moussa.ndiaye@2i-online.com')->first();

        if (!$cuisine || !$awa || !$aminata) {
            $this->command->error('Formations/utilisateurs de démonstration introuvables — lancez FormationsSeeder, FormateurSeeder et EtudiantSeeder avant ForumSeeder.');
            return;
        }

        $posts = [
            [
                'formation' => $cuisine,
                'user' => $awa,
                'title' => 'Astuce pour réussir un fond de veau maison ?',
                'content' => "Bonjour à tous, je galère un peu avec la clarification de mon fond de veau, il reste souvent trouble. Quelqu'un aurait des astuces à partager ?",
                'is_pinned' => false,
                'replies' => [
                    ['user' => $aminata, 'content' => "Bonjour Awa, le secret c'est souvent l'écumage régulier pendant la cuisson, et éviter de faire bouillir trop fort — un frémissement suffit. On reverra ça ensemble au prochain atelier !"],
                    ['user' => $ibrahima, 'content' => "Même souci de mon côté au début, l'écumage a tout changé pour moi aussi."],
                ],
            ],
            [
                'formation' => $cuisine,
                'user' => $aminata,
                'title' => 'Rappel : apportez votre tenue complète pour l\'atelier de jeudi',
                'content' => "Bonjour à toutes et tous, petit rappel que l'atelier pratique de jeudi nécessite la tenue complète (veste, pantalon, toque, chaussures fermées). Merci de votre ponctualité habituelle !",
                'is_pinned' => true,
                'replies' => [
                    ['user' => $awa, 'content' => "Bien reçu, merci pour le rappel !"],
                ],
            ],
            [
                'formation' => $patisserie,
                'user' => $ibrahima,
                'title' => 'Pâte à choux qui retombe après cuisson',
                'content' => "Ma pâte à choux gonfle bien au four mais retombe dès que je les sors. Est-ce un problème de cuisson trop courte ou de four pas assez chaud au départ ?",
                'is_pinned' => false,
                'replies' => [
                    ['user' => $moussa, 'content' => "C'est très probablement une cuisson trop courte — laisse-les dorer un peu plus longtemps et surtout ne pas ouvrir le four avant la fin, sinon le choc thermique les fait retomber."],
                ],
            ],
            [
                'formation' => $serveur,
                'user' => $mariama,
                'title' => 'Comment gérer un client mécontent poliment ?',
                'content' => "On a abordé la théorie en cours mais j'aimerais des exemples concrets de phrases à utiliser face à un client qui se plaint du service. Des idées ?",
                'is_pinned' => false,
                'replies' => [],
            ],
        ];

        foreach ($posts as $postData) {
            $post = ForumPost::create([
                'formation_id' => $postData['formation']->id,
                'user_id' => $postData['user']->id,
                'title' => $postData['title'],
                'content' => $postData['content'],
                'is_pinned' => $postData['is_pinned'],
            ]);

            foreach ($postData['replies'] as $replyData) {
                ForumReply::create([
                    'post_id' => $post->id,
                    'user_id' => $replyData['user']->id,
                    'content' => $replyData['content'],
                ]);
            }
        }

        $this->command->info(count($posts) . ' sujets de forum créés avec succès.');
    }
}
