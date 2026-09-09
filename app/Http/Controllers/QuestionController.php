<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Traits\ChecksFormationOwnership;

/**
 * ATTENTION — sécurité: ce contrôleur n'avait AUCUNE vérification
 * d'accès (ni au niveau route — apiResource complet dans le groupe
 * auth:sanctum général sans restriction de rôle — ni dans le
 * contrôleur). N'importe quel utilisateur connecté, y compris un
 * simple apprenant, pouvait créer/modifier/supprimer N'IMPORTE QUELLE
 * question d'examen ou d'exercice, de n'importe quelle formation —
 * y compris changer la bonne réponse d'un QCM. Corrigé en remontant
 * jusqu'à la formation propriétaire (via exercice->lecon->module, ou
 * via examen directement) et en réutilisant ChecksFormationOwnership,
 * exactement comme ExamenController/ExerciceController le font déjà.
 */
class QuestionController extends Controller
{
    use ChecksFormationOwnership;

    public function index()
    {
        return response()->json([
            'data' => Question::with('reponses')->orderBy('ordre')->get()
        ]);
    }

    public function store(StoreQuestionRequest $request)
    {
        $data = $request->validated();
        $this->authorizeFormationOwner($this->resolveFormationId($data['exercice_id'] ?? null, $data['examen_id'] ?? null));

        $question = Question::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Question créée',
            'data' => $question
        ], 201);
    }

    public function show(Question $question)
    {
        return response()->json([
            'data' => $question->load('reponses')
        ]);
    }

    public function update(UpdateQuestionRequest $request, Question $question)
    {
        $this->authorizeFormationOwner($this->resolveFormationId($question->exercice_id, $question->examen_id));

        $question->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Question modifiée',
            'data' => $question
        ]);
    }

    public function destroy(Question $question)
    {
        $this->authorizeFormationOwner($this->resolveFormationId($question->exercice_id, $question->examen_id));

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question supprimée'
        ]);
    }

    /**
     * Une question appartient soit à un exercice (chaîne
     * exercice -> lecon -> module -> formation), soit directement à un
     * examen (examen -> formation).
     */
    private function resolveFormationId(?int $exerciceId, ?int $examenId): int|string
    {
        if ($exerciceId) {
            $exercice = \App\Models\Exercice::with('lecon.module')->find($exerciceId);
            $formationId = $exercice?->lecon?->module?->formation_id;
        } else {
            $examen = \App\Models\Examen::find($examenId);
            $formationId = $examen?->formation_id;
        }

        if (!$formationId) {
            abort(422, 'Impossible de déterminer la formation associée à cette question');
        }

        return $formationId;
    }
}