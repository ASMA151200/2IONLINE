<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'prenom'=>'required|string|max:255',

            'nom'=>'required|string|max:255',

            'telephone'=>'required|string|max:20',

            'email'=>
            'required|email|unique:users,email',

            'password'=>
            'required|min:8|confirmed',

            'photo'=>
            'nullable|string',

            'role'=>
            'required|in:etudiant,formateur'
        ];
    }

    public function messages()
    {
        return [

            'prenom.required'=>
            'Le prénom est obligatoire',

            'nom.required'=>
            'Le nom est obligatoire',

            'email.unique'=>
            'Email déjà utilisé',

            'role.in'=>
            'Role invalide'
        ];
    }
}