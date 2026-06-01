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

            'texte' => 'sometimes|string',

            'est_correct' => 'sometimes|boolean',

            'ordre' => 'sometimes|integer',

            'question_id' => 'sometimes|exists:questions,id',
        ];
    }
}