<?php

namespace Database\Seeders;

use App\Models\Mentorat;
use App\Models\User;
use Illuminate\Database\Seeder;

class MentoratSeeder extends Seeder
{
    public function run(): void
    {
        Mentorat::query()->delete();

        $bineta = User::where('email', 'bineta.camara@example.com')->first(); // alumni visible
        $aminata = User::where('email', 'aminata.diallo@2i-online.com')->first(); // formatrice
        $awa = User::where('email', 'awa.fall@example.com')->first();
        $ousmane = User::where('email', 'ousmane.diouf@example.com')->first();

        if (!$bineta || !$aminata || !$awa) {
            $this->command->error('Utilisateurs de démonstration introuvables — lancez FormateurSeeder et EtudiantSeeder avant MentoratSeeder.');
            return;
        }

        $mentorats = [
            [
                'mentor_id' => $bineta->id,
                'mentore_id' => $awa->id,
                'statut' => 'actif',
                'message_demande' => "Bonjour Bineta, je suis en CAP Cuisinier comme toi à l'époque et j'aimerais beaucoup avoir tes conseils pour préparer mon entrée dans le monde professionnel. Serais-tu disponible pour m'accompagner ?",
            ],
            [
                'mentor_id' => $aminata->id,
                'mentore_id' => $ousmane->id,
                'statut' => 'en_attente',
                'message_demande' => "Bonjour Chef Aminata, je souhaiterais être guidé sur la suite de mon parcours après le Certificat Professionnel de Spécialité-Cuisinier. Accepteriez-vous de devenir ma mentore ?",
            ],
        ];

        foreach ($mentorats as $data) {
            Mentorat::create($data);
        }

        $this->command->info(count($mentorats) . ' demandes de mentorat créées avec succès.');
    }
}
