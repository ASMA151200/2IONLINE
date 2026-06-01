<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'date' => 'sometimes|date',

            'statut' => 'sometimes|in:actif,termine,annule',

            'formation_id' => 'sometimes|exists:formations,id',
        ];
    }
}