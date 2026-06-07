<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFormationRequest extends FormRequest
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
        return [
            'user_id'     => 'sometimes|exists:users,id',
            'categorie_id'=> 'sometimes|exists:categories,id',
            'titre'       => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'niveau'      => 'sometimes|string',
            'duree'       => 'sometimes|string',
            'prix'        => 'sometimes|numeric',
            'statut'      => 'nullable|in:en ligne,presentiel,hybride',
            'nb_inscrit'  => 'sometimes|integer'
        ];
    }

     public function messages(): array
    {
        return [
            'titre.string'       => 'Le titre doit être une chaîne de caractères',
            'description.text' => 'La description doit être un texte',
            'image.image'        => 'Le fichier doit être une image',
            'image.mimes'        => 'L\'image doit être au format jpg, jpeg, png ou webp',
            'image.max'          => 'L\'image ne doit pas dépasser 2 Mo',
            'statut.in'          => 'Le statut doit être en ligne, presentiel ou hybride',
        ];

    }

}
