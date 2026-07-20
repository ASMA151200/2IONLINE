<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProgressionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'statut'=>
            'sometimes|in:commencer,en cours,termine',

            'lecon_id'=>
            'sometimes|exists:lecons,id',
        ];
    }
}