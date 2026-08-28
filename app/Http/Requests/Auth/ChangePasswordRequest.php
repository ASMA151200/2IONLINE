<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
            'nouveau_password'      => ['required', Password::min(8)->letters()->numbers(), 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'ancien_password.required'      => 'L\'ancien mot de passe est obligatoire',
            'nouveau_password.required'     => 'Le nouveau mot de passe est obligatoire',
            'nouveau_password.confirmed'    => 'La confirmation du mot de passe ne correspond pas',
        ];
    }

}
