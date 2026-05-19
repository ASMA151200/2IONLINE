<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFormateurRequest extends FormRequest
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
            'prenom' =>'sometimes|string',
            'nom' =>'sometimes|string',
            'telephone' =>'sometimes|string',
            'email' => 'sometimes|email|unique:users,email',
            'specialite' =>'sometimes|string',
            'modules' =>'nullable|array',
            'modules.*' =>'exists:modules,id'
        ];
    }
}
