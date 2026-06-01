<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'date' => 'required|date',

            'statut' => 'nullable|in:actif,termine,annule',

            'formation_id' => 'required|exists:formations,id',
        ];
    }

    public function messages(): array
    {
        return [

            'date.required' => 'La date est obligatoire',

            'formation_id.required' => 'La formation est obligatoire',

            'formation_id.exists' => 'Formation introuvable',

            'statut.in' =>
            'Le statut doit être actif, termine ou annule'
        ];
    }
}