<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'prenom' => ['required', 'string', 'max:225'],
            'nom' => ['required', 'string', 'max:225'],
            'telephone' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email'],
            // ATTENTION: 'min:6' sans aucune autre exigence permettait un
            // mot de passe comme "123456" — combiné à l'absence de rate
            // limiting (corrigée séparément), c'était trivialement
            // cassable par force brute. Exige maintenant 8 caractères
            // minimum + au moins une lettre et un chiffre (compromis
            // volontaire : pas de symbole obligatoire, pour ne pas trop
            // pénaliser un public parfois peu technophile).
            'password' => ['required', Password::min(8)->letters()->numbers()],
        ];
    }
}
