<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'titre' => 'sometimes|string|max:255',

            'description' => 'sometimes|string',

            'contenu_html' => 'sometimes|string',

            'image' => 'sometimes|image|mimes:jpg,jpeg,png,webp',

            'type' => 'sometimes|in:actualite,evenement,communique,blog',

            'date_publication' => 'sometimes|date',

            'date_expiration' => 'nullable|date',

            'statut' => 'sometimes|in:brouillon,publie,archive',
        ];
    }
}