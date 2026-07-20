<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReponseRequest extends FormRequest
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
