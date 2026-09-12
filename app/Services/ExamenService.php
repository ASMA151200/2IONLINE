<?php

namespace App\Services;

use App\Models\Examen;
use App\Models\Question;
use App\Models\Resultat;
use App\Models\Reponse;

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
     * enregistre CHAQUE réponse individuellement (table exercice_reponses,
     * partagée avec les exercices via la colonne examen_id — voir
     * migration 2026_09_10_130000), puis agrège en un Resultat.
     *
     * CORRIGÉ: le texte des réponses aux questions ouvertes n'était
     * auparavant jamais enregistré nulle part — seul un score partiel
     * (uniquement les QCM) était calculé, et le contenu réellement écrit
     * par l'étudiant pour toute question ouverte était perdu, empêchant
     * toute correction manuelle ultérieure par le formateur.
     */
    public function soumettre(Examen $examen, int $userId, array $reponses): Resultat
    {
        $questions = $examen->questions()->with('choix')->get()->keyBy('id');

        $scoreObtenu = 0;
        $totalPoints = 0;
        $aQuestionOuverte = false;
        $now = now();
        $rows = [];

        foreach ($questions as $question) {
            $totalPoints += $question->points;
        }

        foreach ($reponses as $reponseData) {
            $question = $questions->get($reponseData['question_id']);
            if (!$question) {
                continue;
            }

            $score = null;
            $statut = 'en_attente';

            if ($question->type === 'qcm' && isset($reponseData['choix_id'])) {
                $choixCorrect = $question->choix->firstWhere('est_correct', true);
                $score = ($choixCorrect && $choixCorrect->id == $reponseData['choix_id']) ? $question->points : 0;
                $scoreObtenu += $score;
                $statut = 'corrige';
            } else {
                // Question ouverte : le score attend une correction
                // manuelle du formateur, mais le texte est désormais
                // bien conservé (reponse_texte) pour qu'il puisse la
                // lire et la noter.
                $aQuestionOuverte = true;
            }

            $rows[] = [
                'exercice_id'   => null,
                'examen_id'     => $examen->id,
                'user_id'       => $userId,
                'question_id'   => $reponseData['question_id'],
                'choix_id'      => $reponseData['choix_id'] ?? null,
                'reponse_texte' => $reponseData['reponse_texte'] ?? null,
                'score'         => $score,
                'statut'        => $statut,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        if ($rows) {
            Reponse::insert($rows);
        }

        $resultat = $this->calculerResultat($examen, $userId, $scoreObtenu, $totalPoints, $aQuestionOuverte);

        return $resultat;
    }

    /**
     * Correction manuelle d'une réponse ouverte d'examen par le
     * formateur — recalcule ensuite le Resultat agrégé de l'étudiant
     * pour cet examen (score total + statut réussi/échoué/en cours),
     * exactement comme le fait ExerciceService::corriger() indirectement
     * via resultatsParEtudiant().
     */
    public function corriger(Reponse $reponse, array $data): Reponse
    {
        $reponse->update([
            'score'                 => $data['score'],
            'commentaire_formateur' => $data['commentaire_formateur'] ?? null,
            'statut'                => 'corrige',
        ]);

        $examen = $reponse->examen()->with('questions')->first();
        $totalPoints = $examen->questions->sum('points');
        $scoreObtenu = Reponse::where('examen_id', $examen->id)
            ->where('user_id', $reponse->user_id)
            ->sum('score');
        $aQuestionOuverte = Reponse::where('examen_id', $examen->id)
            ->where('user_id', $reponse->user_id)
            ->where('statut', 'en_attente')
            ->exists();

        $this->calculerResultat($examen, $reponse->user_id, $scoreObtenu, $totalPoints, $aQuestionOuverte);

        return $reponse;
    }

    private function calculerResultat(Examen $examen, int $userId, int $scoreObtenu, int $totalPoints, bool $aQuestionOuverte): Resultat
    {
        $scoreSur20 = $totalPoints > 0
            ? round(($scoreObtenu / $totalPoints) * $examen->bareme_pts, 2)
            : 0;

        $statut = $aQuestionOuverte
            ? 'en cours'
            : ($scoreSur20 >= ($examen->bareme_pts / 2) ? 'reussi' : 'echoue');

        return Resultat::updateOrCreate(
            ['user_id' => $userId, 'examen_id' => $examen->id],
            ['score' => $scoreSur20, 'date_passage' => now()->toDateString(), 'statut' => $statut],
        );
    }

    /**
     * Détail des réponses d'un étudiant à un examen (une par question) —
     * nécessaire au formateur pour lire et corriger les réponses
     * ouvertes.
     */
    public function resultatsDetail(Examen $examen, int $userId)
    {
        return Reponse::with('question')
            ->where('examen_id', $examen->id)
            ->where('user_id', $userId)
            ->get();
    }
}
