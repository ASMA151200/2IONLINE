<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEtudiantRequest extends FormRequest
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
        // ATTENTION: cette classe existait déjà mais n'était jamais
        // utilisée — EtudiantController::update() appelait
        // StoreEtudiantRequest à la place, dont la règle email
        // "unique:users,email" n'exclut jamais l'utilisateur actuel :
        // modifier un étudiant en conservant son email (le cas normal)
        // échouait donc toujours avec "cet email est déjà pris" — par
        // lui-même. Corrigé ici avec Rule::unique(...)->ignore(), et le
        // contrôleur pointe maintenant vers cette classe.
        $etudiant = $this->route('etudiant');

        return [
            'prenom'          => 'sometimes|string',
            'nom'             => 'sometimes|string',
            'telephone'       => 'sometimes|string',
            'email'           => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($etudiant?->user_id),
            ],
            'date_naissance'  => 'sometimes|date',
            'lieu_naissance'  => 'sometimes|string',
            'niveau'          => 'sometimes|string',
            'formations'      => 'sometimes|array',
            'formations.*'    => 'exists:formations,id',
            ];
    }
}
