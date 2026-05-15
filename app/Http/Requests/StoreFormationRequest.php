<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'titre'=>
            'required|string|max:255',

            'description'=>
            'required',

            'image'=>
            'nullable|string',

            'formateur_id'=>
            'required|exists:users,id',

            'niveau'=>
            'required|string',

            'duree'=>
            'required|string',

            'prix'=>
            'required|numeric',

            'statut'=>
            'required|in:en ligne,presentiel,hybride',

            'categorie_id'=>
            'required|exists:categories,id'

        ];
    }
}