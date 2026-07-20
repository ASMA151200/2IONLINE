<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'montant' => 'required|numeric|min:0',

            'methode' => 'required|in:Wave,Orange Money,Free Money,Virement,CB',

            'date' => 'required|date',

            'formation_id' => 'required|exists:formations,id',
        ];
    }

    public function messages(): array
    {
        return [
            'methode.in' => 'Méthode de paiement invalide',
        ];
    }
}