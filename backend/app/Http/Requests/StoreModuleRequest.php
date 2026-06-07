<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreModuleRequest extends FormRequest
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
            'formation_id' => 'required|exists:formations,id',
            'titre'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'ordre'        => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'formation_id.required' => 'La formation est obligatoire',
            'formation_id.exists'   => 'Cette formation n\'existe pas',
            'titre.required'        => 'Le titre est obligatoire',
        ];
    }

}
