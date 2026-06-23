<?php

namespace App\Services;

use App\Models\Exercice;
use App\Models\Question;
use App\Models\Reponse;

class ExerciceService
{
    // Liste des exercices d'une leçon
    public function getByLecon(int $leconId){
        return Exercice::with('questions.choix')
                       ->where('lecon_id', $leconId)
                       ->get();
    }

    // Créer un exercice avec ses questions et choix
    public function create(array $data): Exercice
    {
        // Créer l'exercice
        $exercice = Exercice::create([
            'lecon_id'    => $data['lecon_id'],
            'titre'       => $data['titre'],
            'description' => $data['description'] ?? null,
            'type'        => $data['type'],
            'duree'       => $data['duree'] ?? null,
            'note_max'    => $data['note_max'] ?? 20,
        ]);

        // Créer les questions et leurs choix
        foreach ($data['questions'] as $index => $questionData) {
            $question = $exercice->questions()->create([
                'contenu' => $questionData['contenu'],
                'type'    => $questionData['type'],
                'points'  => $questionData['points'] ?? 1,
                'ordre'   => $questionData['ordre'] ?? $index,
            ]);

            // Créer les choix si QCM
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

        return $exercice->load('questions.choix');
    }

    // Soumettre les réponses d'un etudiant
    public function soumettre(Exercice $exercice, int $userId, array $reponses): array
    {
        $scoreTotal = 0;
        $reponsesCreees = [];

        foreach ($reponses as $reponseData) {
            $question = Question::findOrFail($reponseData['question_id']);
            $score = null;
            $statut = 'en_attente';

            // Correction automatique pour QCM
            if ($question->type === 'qcm' && isset($reponseData['choix_id'])) {
                $choixCorrect = $question->choix()->where('est_correct', true)->first();
                $score = ($choixCorrect && $choixCorrect->id == $reponseData['choix_id'])
                    ? $question->points
                    : 0;
                $scoreTotal += $score;
                $statut = 'corrige';
            }

            $reponsesCreees[] = Reponse::create([
                'exercice_id'   => $exercice->id,
                'user_id'       => $userId,
                'question_id'   => $reponseData['question_id'],
                'choix_id'      => $reponseData['choix_id'] ?? null,
                'reponse_texte' => $reponseData['reponse_texte'] ?? null,
                'score'         => $score,
                'statut'        => $statut,
            ]);
        }

        return [
            'reponses'    => $reponsesCreees,
            'score_total' => $scoreTotal,
        ];
    }

    // Correction manuelle par le formateur
    public function corriger(Reponse $reponse, array $data): Reponse
    {
        $reponse->update([
            'score'                 => $data['score'],
            'commentaire_formateur' => $data['commentaire_formateur'] ?? null,
            'statut'                => 'corrige',
        ]);

        return $reponse;
    }

    // Résultats d'un apprenant pour un exercice
    public function resultats(Exercice $exercice, int $userId)
    {
        return Reponse::with(['question', 'choix'])
                               ->where('exercice_id', $exercice->id)
                               ->where('user_id', $userId)
                               ->get();
    }

    // Supprimer un exercice
    public function delete(Exercice $exercice): void
    {
        $exercice->deleteOrFail();
    }
}
