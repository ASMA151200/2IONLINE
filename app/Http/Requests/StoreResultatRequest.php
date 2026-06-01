<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResultatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'score' => 'required|numeric|min:0',

            'date_passage' => 'required|date',

            'statut' => 'required|in:reussi,echoue,en cours',

            'examen_id' => 'required|exists:examens,id',
        ];
    }
}