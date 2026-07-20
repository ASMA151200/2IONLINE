<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExamenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'type' => 'sometimes|in:quiz,examen',

            'titre' => 'sometimes|string|max:255',

            'description' => 'nullable|string',

            'duree_minutes' => 'sometimes|integer|min:1',

            'bareme_pts' => 'sometimes|integer|min:1',

            'formation_id' => 'sometimes|exists:formations,id',
        ];
    }
}