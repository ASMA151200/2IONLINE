<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFormationRequest extends FormRequest
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
           // 'user_id'     => 'required|exists:users,id',
            'categorie_id'=> 'required|exists:categories,id',
            'titre'       => 'required|string|max:255',
            'description' => 'required|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'niveau'      => 'required|string',
            'duree'       => 'required|string',
            'prix'        => 'required|numeric',
            'statut'      => 'nullable|in:en ligne,presentiel,hybride',
            'nb_inscrit'  => 'required|integer'
        ];
    }


    public function messages(): array
    {
        return [
            'titre.required'       => 'Le titre est obligatoire',
            'description.required' => 'La description est obligatoire',
            'image.image'          => 'Le fichier doit être une image',
            'image.mimes'          => 'L\'image doit être au format jpg, jpeg, png ou webp',
            'image.max'            => 'L\'image ne doit pas dépasser 2 Mo',
            'statut.in'            => 'Le statut doit être en ligne, hybride ou presentiel',
        ];
    }
}
