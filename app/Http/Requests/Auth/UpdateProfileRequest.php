<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'prenom' => ['sometimes', 'string', 'max:225'],
            'nom' => ['sometimes', 'string', 'max:225'],
            'telephone' => ['sometimes', 'string', 'max:50'],
            'photo' => ['sometimes', 'nullable', 'file', 'image', 'max:2048'],
        ];
    }
}
