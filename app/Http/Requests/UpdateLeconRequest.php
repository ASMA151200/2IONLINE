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
            // CORRIGÉ: "|51200" sans le préfixe "max:" n'est pas une règle
            // Laravel valide (règle silencieusement invalide ou erreur
            // selon le contexte) — la limite de taille n'était donc
            // jamais réellement appliquée en modification.
            'video' =>'nullable|file|mimes:mp4,mov,avi,webm,mpeg,mpg|max:51200',
            'video_url' => 'nullable|url|max:500',
            // CORRIGÉ: "docs" n'est pas une extension valide (c'est
            // "docx" qui existe, déjà présent juste après en double).
            'document' =>'nullable|file|mimes:pdf,docx,doc,pptx,ppt|max:20480',
            'ordre' =>'sometimes|integer|min:1'
        ];
    }
}
