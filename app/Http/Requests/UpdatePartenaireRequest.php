<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartenaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ignore l'email du partenaire lui-même dans la vérification
        // d'unicité (sinon modifier un partenaire SANS changer son email
        // échouerait toujours, puisque cet email "existe déjà" — c'est
        // le sien).
        $partenaire = $this->route('partenaire');

        return [
            'prenom' => 'sometimes|string',
            'nom' => 'sometimes|string',
            'telephone' => 'sometimes|string',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($partenaire?->user_id),
            ],
            'nom_organisation' => 'sometimes|string',
            'secteur' => 'nullable|string',
        ];
    }
}
