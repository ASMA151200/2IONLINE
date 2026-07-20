<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreExerciceRequest extends FormRequest
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
        'lecon_id'    => 'required|exists:lecons,id',
        'titre'       => 'required|string|max:255',
        'description' => 'nullable|string',
        'type'        => 'required|in:qcm,ouvert,mixte',
        'duree'       => 'nullable|integer|min:1',
        'note_max'    => 'nullable|integer|min:1',

        // Questions
        'questions'                => 'required|array|min:1',
        'questions.*.contenu'      => 'required|string',
        'questions.*.type'         => 'required|in:qcm,ouvert',
        'questions.*.points'       => 'nullable|integer|min:1',
        'questions.*.ordre'        => 'nullable|integer',

        // Choix (pour QCM)
        'questions.*.choix'                  => 'required_if:questions.*.type,qcm|array|min:2',
        'questions.*.choix.*.contenu'        => 'required|string',
        'questions.*.choix.*.est_correct'    => 'required|boolean',
        'questions.*.choix.*.ordre'          => 'nullable|integer',
    ];
}

    public function messages(): array
    {
        return [
            'lecon_id.required'              => 'La leçon est obligatoire',
            'lecon_id.exists'                => 'Cette leçon n\'existe pas',
            'titre.required'                 => 'Le titre est obligatoire',
            'type.required'                  => 'Le type est obligatoire',
            'type.in'                        => 'Le type doit être qcm, ouvert ou mixte',
            'questions.required'             => 'Au moins une question est obligatoire',
            'questions.*.contenu.required'   => 'Le contenu de la question est obligatoire',
            'questions.*.type.required'      => 'Le type de la question est obligatoire',
            'questions.*.choix.required_if'  => 'Les choix sont obligatoires pour un QCM',
        ];
        }
    }
