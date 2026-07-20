<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'texte' => 'sometimes|string',

            'type' => 'sometimes|in:QCM,VF,ouvert',

            'points' => 'sometimes|integer|min:1',

            'ordre' => 'sometimes|integer',

            'examen_id' => 'sometimes|exists:examens,id',
        ];
    }
}