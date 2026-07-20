<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'titre' => 'required|string|max:255',

            'description' => 'nullable|string',

            'contenu_html' => 'required|string',

            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp',

            'type' => 'required|in:actualite,evenement,communique,blog',

            'date_publication' => 'required|date',

            'date_expiration' => 'nullable|date|after_or_equal:date_publication',

            'statut' => 'required|in:brouillon,publie,archive',
        ];
    }
}