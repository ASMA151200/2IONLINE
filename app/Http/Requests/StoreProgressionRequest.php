<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProgressionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'statut'=>
            'required|in:commencer,en cours,termine',

            'lecon_id'=>
            'required|exists:lecons,id',
        ];
    }

    public function messages(): array
    {
        return [

            'statut.required'=>
            'Le statut est obligatoire',

            'statut.in'=>
            'Statut invalide',

            'lecon_id.required'=>
            'La leçon est obligatoire',

            'lecon_id.exists'=>
            'Leçon introuvable'
        ];
    }
}