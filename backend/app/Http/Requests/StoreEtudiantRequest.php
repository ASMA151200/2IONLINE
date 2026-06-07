<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEtudiantRequest extends FormRequest
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
            'prenom'          => 'required|string',
            'nom'             => 'required|string',
            'telephone'       => 'required|string',
            'email'           => 'required|email|unique:users,email',
            'date_naissance'  => 'required|date',
            'lieu_naissance'  => 'required|string',
            'niveau'          => 'required|string',
            'formations'      => 'required|array',
            'formations.*'    => 'exists:formations,id',
        ];
    }
}
