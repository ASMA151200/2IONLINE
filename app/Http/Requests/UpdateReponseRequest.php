<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reponse_texte' => 'sometimes|nullable|string',

            'choix_id' => 'sometimes|nullable|exists:choix,id',

            'score' => 'sometimes|nullable|numeric',

            'statut' => 'sometimes|in:en_attente,corrige',

            'commentaire_formateur' => 'sometimes|nullable|string',
        ];
    }
}