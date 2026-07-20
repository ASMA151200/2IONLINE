<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

<<<<<<< HEAD
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array{
        return [
        'reponses'                  => 'required|array|min:1',
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
=======
    public function rules(): array
    {
        return [

            'texte' => 'required|string',

            'est_correct' => 'required|boolean',

            'ordre' => 'required|integer',

            'question_id' => 'required|exists:questions,id',
        ];
    }
}
>>>>>>> e3871b226d875bbe91500d6a0b07ef7e9e9c49ca
