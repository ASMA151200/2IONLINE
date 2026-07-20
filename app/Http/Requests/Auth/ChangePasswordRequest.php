<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'ancien_password'       => 'required|string',
            'nouveau_password'      => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'ancien_password.required'      => 'L\'ancien mot de passe est obligatoire',
            'nouveau_password.required'     => 'Le nouveau mot de passe est obligatoire',
            'nouveau_password.min'          => 'Le mot de passe doit contenir au moins 6 caractères',
            'nouveau_password.confirmed'    => 'La confirmation du mot de passe ne correspond pas',
        ];
    }

}
