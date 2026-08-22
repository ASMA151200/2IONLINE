<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation dédiée pour ReponseController::store() (POST /v1/reponses
 * générique, une seule réponse à la fois) — distincte de StoreReponseRequest
 * qui valide la soumission GROUPÉE via ExerciceController::soumettre()
 * (tableau "reponses"). Les deux routes réutilisaient auparavant la même
 * classe de validation avec des formes de données incompatibles.
 */
class StoreSingleReponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exercice_id' => 'required|exists:exercices,id',
            'question_id' => 'required|exists:exercice_questions,id',
            'choix_id' => 'nullable|exists:choix,id',
            'reponse_texte' => 'nullable|string',
        ];
    }
}
