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

    // Mettre à jour un exercice avec ses questions et choix
    public function update(Exercice $exercice, array $data): Exercice
    {
        // Mettre à jour les champs de l'exercice
        $exercice->update([
            'titre'       => $data['titre'] ?? $exercice->titre,
            'description' => $data['description'] ?? $exercice->description,
            'type'        => $data['type'] ?? $exercice->type,
            'duree'       => $data['duree'] ?? $exercice->duree,
            'note_max'    => $data['note_max'] ?? $exercice->note_max,
        ]);

        // Mettre à jour les questions si fournies
        if (isset($data['questions'])) {
            // Supprimer les questions (et leurs choix via cascade) absentes du payload
            $questionIdsPayload = collect($data['questions'])->pluck('id')->filter();
            $exercice->questions()->whereNotIn('id', $questionIdsPayload)->delete();

            foreach ($data['questions'] as $index => $questionData) {
                // Update ou create selon présence de l'id
                $question = $exercice->questions()->updateOrCreate(
                    ['id' => $questionData['id'] ?? null],
                    [
                        'contenu' => $questionData['contenu'],
                        'type'    => $questionData['type'],
                        'points'  => $questionData['points'] ?? 1,
                        'ordre'   => $questionData['ordre'] ?? $index,
                    ]
                );

                // Mettre à jour les choix si QCM
                if ($questionData['type'] === 'qcm' && isset($questionData['choix'])) {
                    $choixIdsPayload = collect($questionData['choix'])->pluck('id')->filter();
                    $question->choix()->whereNotIn('id', $choixIdsPayload)->delete();

                    foreach ($questionData['choix'] as $i => $choixData) {
                        $question->choix()->updateOrCreate(
                            ['id' => $choixData['id'] ?? null],
                            [
                                'contenu'     => $choixData['contenu'],
                                'est_correct' => $choixData['est_correct'],
                                'ordre'       => $choixData['ordre'] ?? $i,
                            ]
                        );
                    }
                } else {
                    // Si le type n'est plus QCM, supprimer les anciens choix
                    $question->choix()->delete();
                }
            }
        }

        return $exercice->load('questions.choix');
    }

    // Soumettre les réponses d'un etudiant
    public function soumettre(Exercice $exercice, int $userId, array $reponses): array
    {
        // CORRIGÉ: exécutait auparavant Question::findOrFail() PUIS
        // ->choix()->where(...)->first() pour CHAQUE réponse — pour un
        // exercice de 10 questions, ça faisait ~20-30 requêtes à chaque
        // soumission (un événement fréquent, contrairement à la création
        // d'exercice). Précharge maintenant toutes les questions
        // concernées avec leurs choix en 2 requêtes au total, peu
        // importe le nombre de questions.
        $questionIds = collect($reponses)->pluck('question_id');
        $questions = Question::with('choix')->whereIn('id', $questionIds)->get()->keyBy('id');

        $scoreTotal = 0;
        $rowsToInsert = [];
        $now = now();

        foreach ($reponses as $reponseData) {
            $question = $questions->get($reponseData['question_id']);
            if (!$question) {
                continue; // ID de question invalide envoyé — ignoré silencieusement, comme findOrFail l'aurait fait échouer sur celle-ci seulement
            }

            $score = null;
            $statut = 'en_attente';

            // Correction automatique pour QCM
            if ($question->type === 'qcm' && isset($reponseData['choix_id'])) {
                $choixCorrect = $question->choix->firstWhere('est_correct', true);
                $score = ($choixCorrect && $choixCorrect->id == $reponseData['choix_id'])
                    ? $question->points
                    : 0;
                $scoreTotal += $score;
                $statut = 'corrige';
            }

            $rowsToInsert[] = [
                'exercice_id'   => $exercice->id,
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

        Reponse::insert($rowsToInsert);

        // Reponse::insert() (insertion en masse) ne renvoie pas les
        // modèles créés avec leurs IDs — on les recharge en une seule
        // requête pour garder le même format de retour qu'avant.
        $reponsesCreees = Reponse::where('exercice_id', $exercice->id)
            ->where('user_id', $userId)
            ->whereIn('question_id', $questionIds)
            ->get();

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
