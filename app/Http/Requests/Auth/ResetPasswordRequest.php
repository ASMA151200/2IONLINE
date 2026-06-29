<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use SebastianBergmann\Type\TrueType;

class ResetPasswordRequest extends FormRequest
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
            'email'                 => 'required|email|exists:users,email',
            'code'                  => 'required|string',
            'nouveau_password'      => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'                => 'L\'email est obligatoire',
            'email.exists'                  => 'Aucun compte associé à cet email',
            'code.required'                 => 'Le code est obligatoire',
            'nouveau_password.required'     => 'Le nouveau mot de passe est obligatoire',
            'nouveau_password.confirmed'    => 'La confirmation ne correspond pas',
        ];
    }

}
