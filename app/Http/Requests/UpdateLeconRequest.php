<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLeconRequest extends FormRequest
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
            'module_id' =>'sometimes|exists:modules,id',
            'titre' =>'sometimes|string|max:255',
            'contenu' =>'sometimes|string',
            'video' =>'nullable|file|mimes:mp4,mov,avi,webm|51200',
            'document' =>'nullable|file|mimes:pdf,docs,doc,pptx,ppt|max:20480',
            'ordre' =>'sometimes|integer|min:1'
        ];
    }
}
