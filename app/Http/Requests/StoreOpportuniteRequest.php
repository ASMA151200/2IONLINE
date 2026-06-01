<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpportuniteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'titre' => 'required|string',

            'type' => 'required|in:stage,emploi,formation,bourse,partenariat',

            'description' => 'required|string',

            'documents' => 'nullable|file|mimes:pdf',

            'date_debut' => 'required|date',

            'date_fin' => 'required|date|after_or_equal:date_debut',

            'ville' => 'required|string',

            'pays' => 'required|string',

            'entreprise' => 'nullable|string',

            'lien_inscription' => 'nullable|url',

            'statut' => 'required|in:ouvert,ferme,en cours',
        ];
    }
}