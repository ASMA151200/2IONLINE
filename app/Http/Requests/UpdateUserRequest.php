<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId=$this->route('user')->id;

        return [

            'prenom'=>
            'sometimes|string|max:255',

            'nom'=>
            'sometimes|string|max:255',

            'telephone'=>
            'sometimes|string|max:20',

            'email'=>
            'sometimes|email|unique:users,email,'.$userId,

            'photo'=>
            'nullable|string',

            'role'=>
            'sometimes|in:admin,etudiant,formateur'
        ];
    }
}