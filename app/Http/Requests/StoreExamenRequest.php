<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExamenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:quiz,examen',

            'titre' => 'required|string|max:255',

            'description' => 'nullable|string',

            'duree_minutes' => 'required|integer|min:1',

            'bareme_pts' => 'required|integer|min:1',

            'formation_id' => 'required|exists:formations,id',

            // Questions (optionnel à la création — un examen peut aussi
            // être créé "vide" puis complété via /v1/questions séparément,
            // mais la structure imbriquée est recommandée, comme pour les
            // exercices).
            'questions'                       => 'nullable|array|min:1',
            'questions.*.contenu'             => 'required_with:questions|string',
            'questions.*.type'                => 'required_with:questions|in:qcm,ouvert',
            'questions.*.points'              => 'nullable|integer|min:1',
            'questions.*.ordre'               => 'nullable|integer',

            'questions.*.choix'                       => 'required_if:questions.*.type,qcm|array|min:2',
            'questions.*.choix.*.contenu'             => 'required|string',
            'questions.*.choix.*.est_correct'         => 'required|boolean',
            'questions.*.choix.*.ordre'               => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type est obligatoire',
            'type.in' => 'Le type doit être quiz ou examen',

            'titre.required' => 'Le titre est obligatoire',

            'duree_minutes.required' => 'La durée est obligatoire',
            'duree_minutes.integer' => 'La durée doit être un nombre',

            'bareme_pts.required' => 'Le barème est obligatoire',

            'formation_id.required' => 'La formation est obligatoire',
            'formation_id.exists' => 'Formation introuvable',
        ];
    }
}