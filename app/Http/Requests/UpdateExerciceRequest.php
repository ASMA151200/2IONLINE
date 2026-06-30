<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre'           => 'sometimes|string|max:255',
            'description'     => 'nullable|string',
            'type'            => 'sometimes|in:qcm,texte,mixte',
            'duree'           => 'nullable|integer|min:1',
            'note_max'        => 'sometimes|numeric|min:0',

            'questions'         => 'sometimes|array',
            'questions.*.id'    => 'nullable|integer|exists:questions,id',
            'questions.*.contenu'     => 'required_with:questions|string',
            'questions.*.type'        => 'required_with:questions|in:qcm,texte',
            'questions.*.points'      => 'nullable|numeric|min:0',
            'questions.*.ordre'       => 'nullable|integer|min:0',

            'questions.*.choix'               => 'required_if:questions.*.type,qcm|array',
            'questions.*.choix.*.id'          => 'nullable|integer|exists:choix,id',
            'questions.*.choix.*.contenu'     => 'required_with:questions.*.choix|string',
            'questions.*.choix.*.est_correct' => 'required_with:questions.*.choix|boolean',
            'questions.*.choix.*.ordre'       => 'nullable|integer|min:0',
        ];

    }

    public function messages(): array
    {
        return [
            'titre.string'        => 'Le titre doit être une chaîne de caractères.',
            'type.in'             => 'Le type doit être : qcm, texte ou mixte.',
            'duree.integer'       => 'La durée doit être un entier.',
            'note_max.numeric'    => 'La note maximale doit être un nombre.',

            'questions.array'              => 'Les questions doivent être un tableau.',
            'questions.*.id.exists'         => 'Une question fournie est introuvable.',
            'questions.*.contenu.required_with'       => 'Le contenu de chaque question est obligatoire.',
            'questions.*.type.required_with'          => 'Le type de chaque question est obligatoire.',
            'questions.*.type.in'                     => 'Le type de question doit être : qcm ou texte.',

            'questions.*.choix.required_if'           => 'Les choix sont obligatoires pour une question QCM.',
            'questions.*.choix.*.id.exists'           => 'Un choix fourni est introuvable.',
            'questions.*.choix.*.contenu.required_with'      => 'Le contenu de chaque choix est obligatoire.',
            'questions.*.choix.*.est_correct.required_with'  => 'Le champ est_correct de chaque choix est obligatoire.',
        ];
    }

}
