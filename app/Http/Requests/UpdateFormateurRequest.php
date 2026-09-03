<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormateurRequest extends FormRequest
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
        // ATTENTION: bug repéré il y a longtemps (voir le commentaire
        // équivalent dans UpdatePartenaireRequest) mais jamais corrigé
        // ICI — "unique:users,email" sans exception pour l'utilisateur
        // actuel faisait échouer TOUTE modification d'un formateur tant
        // que son email restait inchangé (le cas normal), avec l'erreur
        // "the email has already been taken" — par lui-même. C'est
        // précisément ce qui bloquait l'assignation d'une formation à un
        // professeur (l'email non modifié suffisait à faire échouer
        // toute la requête avant même que formation_id ne soit traité).
        $formateur = $this->route('formateur');

        return [
            'prenom' =>'sometimes|string',
            'nom' =>'sometimes|string',
            'telephone' =>'sometimes|string',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($formateur?->user_id),
            ],
            'specialite' =>'sometimes|string',
            'modules' =>'nullable|array',
            'modules.*' =>'exists:modules,id',
            'formation_id' => 'nullable|exists:formations,id',
        ];
    }
}
