<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array{
        return [
        'reponses'                  => 'required|array|min:1',
        // Le modèle Eloquent Question pointe réellement vers la table
        // "exercice_questions" (protected $table = 'exercice_questions'
        // dans app/Models/Question.php), PAS vers la table "questions"
        // (qui existe mais n'est utilisée par aucun modèle actuellement).
        'reponses.*.question_id'    => 'required|exists:exercice_questions,id',
        'reponses.*.choix_id'       => 'nullable|exists:choix,id',
        'reponses.*.reponse_texte'  => 'nullable|string',
    ];
    }

    public function messages(): array{
        return [
            'reponses.required'                 => 'Au moins une réponse est obligatoire',
            'reponses.*.question_id.required'   => 'La question est obligatoire',
            'reponses.*.question_id.exists'     => 'Cette question n\'existe pas',
        ];
    }


}
