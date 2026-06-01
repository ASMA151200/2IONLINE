<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'texte' => 'required|string',

            'type' => 'required|in:QCM,VF,ouvert',

            'points' => 'required|integer|min:1',

            'ordre' => 'required|integer',

            'examen_id' => 'required|exists:examens,id',
        ];
    }
}