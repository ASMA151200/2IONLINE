<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeconRequest extends FormRequest
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
            'module_id' =>'required|exists:modules,id',
            'titre' =>'required|string|max:255',
            'contenu' =>'required|string',
            'video' => 'nullable|file|mimes:mp4,mov,avi,webm,mpeg,mpg|max:51200',
            'document' =>'nullable|file|mimes:pdf,docx,doc,pptx,ppt|max:20480',
            'ordre' =>'required|integer|min:1'

        ];
    }

    public function messages(): array
    {
        return [

            'module_id.exists' =>'Le module sélectionné n’existe pas.',

            'video.mimes' =>'Le format vidéo est invalide.',

            'document.mimes' =>'Le format du document est invalide.',
        ];
    }

}
