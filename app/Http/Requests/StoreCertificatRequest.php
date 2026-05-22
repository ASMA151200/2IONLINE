<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero_certificat' => [
                'required',
                'string',
                'max:255',
                'unique:certificats,numero_certificat'
            ],

            'fichier_pdf' => [
                'required',
                'file',
                'mimes:pdf',
                'max:5120'
            ],

            'date_obtention' => [
                'required',
                'date'
            ],

            'user_id' => [
                'required',
                'exists:users,id'
            ],

            'formation_id' => [
                'required',
                'exists:formations,id'
            ],
        ];
    }
}