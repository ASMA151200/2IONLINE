<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'montant' => 'sometimes|numeric|min:0',

            'methode' => 'sometimes|in:Wave,Orange Money,Free Money,Virement,CB',

            'statut' => 'sometimes|in:en attente,confirme,echec',

            'date' => 'sometimes|date',

            'formation_id' => 'sometimes|exists:formations,id',
        ];
    }
}