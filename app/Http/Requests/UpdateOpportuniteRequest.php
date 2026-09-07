<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOpportuniteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'titre' => 'sometimes|string',

            'type' => 'sometimes|in:stage,emploi,formation,bourse,partenariat',

            'description' => 'sometimes|string',

            'documents' => 'sometimes|file|mimes:pdf',

            'image' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:5120',

            'date_debut' => 'sometimes|date',

            'date_fin' => 'sometimes|date',

            'ville' => 'sometimes|string',

            'pays' => 'sometimes|string',

            'entreprise' => 'nullable|string',

            'lien_inscription' => 'nullable|url',

            'statut' => 'sometimes|in:ouvert,ferme,en cours',
        ];
    }
}