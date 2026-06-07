<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEtudiantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prenom'          => 'sometimes|string',
            'nom'             => 'sometimes|string',
            'telephone'       => 'sometimes|string',
            'email'           => 'sometimes|email|unique:users,email,'.$this->etudiant->user_id,
            'date_naissance'  => 'sometimes|date',
            'lieu_naissance'  => 'sometimes|string',
            'niveau'          => 'somtimes|string',
            'formations'      => 'somtimes|array',
            'formations.*'    => 'exists:formations,id',
            ];
    }
}
