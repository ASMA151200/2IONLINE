<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateInscriptionRequest extends FormRequest
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
            'formation_id'     => 'sometimes|exists:formations,id',
            //'user_id'           => 'sometimes|exists:user,id',
            //'date_inscription' => 'sometimes|date',
            //'statut'            => 'nullable|in:actif,termine,annule'
        ];
    }
}
