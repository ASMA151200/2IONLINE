<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePartenaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prenom' => 'required|string',
            'nom' => 'required|string',
            'telephone' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'nom_organisation' => 'required|string',
            'secteur' => 'nullable|string',
        ];
    }
}
