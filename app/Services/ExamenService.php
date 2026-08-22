<?php

namespace App\Services;

use App\Models\Examen;
use App\Models\Question;
use App\Models\Resultat;

class ExamenService
{
    /**
     * Crée un examen avec ses questions et choix imbriqués (même principe
     * que ExerciceService::create()).
     */
    public function create(array $data): Examen
    {
        $examen = Examen::create([
            'type' => $data['type'],
            'titre' => $data['titre'],
            'description' => $data['description'] ?? null,
            'duree_minutes' => $data['duree_minutes'],
            'bareme_pts' => $data['bareme_pts'],
            'formation_id' => $data['formation_id'],
        ]);

        foreach (($data['questions'] ?? []) as $index => $questionData) {
            $question = $examen->questions()->create([
                'contenu' => $questionData['contenu'],
                'type'    => $questionData['type'],
                'points'  => $questionData['points'] ?? 1,
                'ordre'   => $questionData['ordre'] ?? $index,
            ]);

            if ($questionData['type'] === 'qcm' && isset($questionData['choix'])) {
                foreach ($questionData['choix'] as $i => $choixData) {
                    $question->choix()->create([
                        'contenu'     => $choixData['contenu'],
                        'est_correct' => $choixData['est_correct'],
                        'ordre'       => $choixData['ordre'] ?? $i,
                    ]);
                }
            }
        }

        return $examen->load('questions.choix');
    }

    /**
     * Passage d'un examen par un étudiant : note automatiquement les QCM,
     * calcule un score total ramené au barème de l'examen (bareme_pts), et
     * enregistre le résultat dans la table resultats.
     *
     * ATTENTION: contrairement aux exercices (qui gardent chaque réponse
     * individuelle dans "reponses"), la table resultats ne stocke qu'un
     * score final agrégé — le détail question par question n'est pas
     * conservé pour les examens. Si un historique détaillé est nécessaire,
     * il faudra une table dédiée (ex: examen_reponses).
     */
    public function soumettre(Examen $examen, int $userId, array $reponses): Resultat
    {
        $questions = $examen->questions()->with('choix')->get()->keyBy('id');

        $scoreObtenu = 0;
        $totalPoints = 0;
        $aQuestionOuverte = false;

        foreach ($questions as $question) {
            $totalPoints += $question->points;
        }

        foreach ($reponses as $reponseData) {
            $question = $questions->get($reponseData['question_id']);
            if (!$question) {
                continue;
            }

            if ($question->type === 'qcm' && isset($reponseData['choix_id'])) {
                $choixCorrect = $question->choix->firstWhere('est_correct', true);
                if ($choixCorrect && $choixCorrect->id == $reponseData['choix_id']) {
                    $scoreObtenu += $question->points;
                }
            } else {
                // Question ouverte : pas de correction automatique possible,
                // le score final nécessitera une correction manuelle du
                // formateur (le résultat est enregistré "en_cours" en
                // attendant).
                $aQuestionOuverte = true;
            }
        }

        $scoreSur20 = $totalPoints > 0
            ? round(($scoreObtenu / $totalPoints) * $examen->bareme_pts, 2)
            : 0;

        $statut = $aQuestionOuverte
            ? 'en cours'
            : ($scoreSur20 >= ($examen->bareme_pts / 2) ? 'reussi' : 'echoue');

        return Resultat::create([
            'score' => $scoreSur20,
            'date_passage' => now()->toDateString(),
            'statut' => $statut,
            'user_id' => $userId,
            'examen_id' => $examen->id,
        ]);
    }
}
