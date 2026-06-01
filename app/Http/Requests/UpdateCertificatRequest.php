<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCertificatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'numero_certificat' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('certificats')
                    ->ignore($this->certificat)
            ],

            'fichier_pdf' => [
                'sometimes',
                'file',
                'mimes:pdf',
                'max:5120'
            ],

            'date_obtention' => [
                'sometimes',
                'date'
            ],

            'user_id' => [
                'sometimes',
                'exists:users,id'
            ],

            'formation_id' => [
                'sometimes',
                'exists:formations,id'
            ],
        ];
    }
}