<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\Sondage;
use App\Models\SondageReponse;
use App\Models\User;
use Illuminate\Database\Seeder;

class SondageSeeder extends Seeder
{
    public function run(): void
    {
        SondageReponse::query()->delete();
        Sondage::query()->delete();

        $cuisine = Formation::where('titre', 'CAP Cuisinier')->first();
        $patisserie = Formation::where('titre', 'CAP Pâtissier')->first();

        $awa = User::where('email', 'awa.fall@example.com')->first();
        $ibrahima = User::where('email', 'ibrahima.sow@example.com')->first();

        if (!$cuisine || !$awa) {
            $this->command->error('Formations/utilisateurs de démonstration introuvables — lancez FormationsSeeder et EtudiantSeeder avant SondageSeeder.');
            return;
        }

        $sondageCuisine = Sondage::create([
            'formation_id' => $cuisine->id,
            'titre' => 'Satisfaction — premier trimestre CAP Cuisinier',
            'questions' => [
                ['id' => 'q1', 'texte' => 'Comment évaluez-vous la qualité des ateliers pratiques ?', 'type' => 'note'],
                ['id' => 'q2', 'texte' => 'Le rythme de la formation vous convient-il ?', 'type' => 'note'],
                ['id' => 'q3', 'texte' => 'Qu\'aimeriez-vous voir amélioré dans la suite du programme ?', 'type' => 'texte'],
            ],
        ]);

        SondageReponse::create([
            'sondage_id' => $sondageCuisine->id,
            'user_id' => $awa->id,
            'reponses' => [
                ['question_id' => 'q1', 'valeur' => 5],
                ['question_id' => 'q2', 'valeur' => 4],
                ['question_id' => 'q3', 'valeur' => "Peut-être un peu plus de temps sur les cuissons de viande, sinon je suis très satisfaite du programme."],
            ],
        ]);

        $sondagePatisserie = Sondage::create([
            'formation_id' => $patisserie->id,
            'titre' => 'Retour sur le module Pâte à choux et viennoiseries',
            'questions' => [
                ['id' => 'q1', 'texte' => 'Le formateur a-t-il bien expliqué les techniques de base ?', 'type' => 'note'],
                ['id' => 'q2', 'texte' => 'Avez-vous des suggestions pour ce module ?', 'type' => 'texte'],
            ],
        ]);

        SondageReponse::create([
            'sondage_id' => $sondagePatisserie->id,
            'user_id' => $ibrahima->id,
            'reponses' => [
                ['question_id' => 'q1', 'valeur' => 5],
                ['question_id' => 'q2', 'valeur' => "Peut-être une vidéo de démonstration supplémentaire sur le façonnage des éclairs."],
            ],
        ]);

        $this->command->info('2 sondages (avec réponses) créés avec succès.');
    }
}
