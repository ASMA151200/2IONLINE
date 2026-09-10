<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFormateurRequest extends FormRequest
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
            'prenom' =>'required|string',
            'nom' =>'required|string',
            'telephone' =>'required|string',
            'email' => 'required|email|unique:users,email',
            'specialite' =>'required|string',
            'modules' =>'nullable|array',
            'modules.*' =>'exists:modules,id',
            // Un formateur peut désormais intervenir dans plusieurs
            // formations (voir FormateurService, table pivot
            // formation_formateur) — accepte un tableau, plus un
            // formation_id unique.
            'formation_ids' => 'nullable|array',
            'formation_ids.*' => 'exists:formations,id',

        ];
    }
}
