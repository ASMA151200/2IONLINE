<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'prenom' =>$this->prenom,
            'nom' =>$this->nom,
            'telephone' =>$this->telephone,
            'email' =>$this->email,
            'role' =>$this->role,
            // ATTENTION: ce champ n'était jamais exposé du tout — l'upload
            // fonctionnait (fichier stocké, colonne mise à jour), mais le
            // frontend n'avait aucun moyen de savoir que la photo existait
            // ni où la trouver ("s'ajoute mais ne s'affiche jamais").
            // Retourne une URL complète et exploitable directement, pas
            // le chemin relatif brut ("avatars/xxx.jpg") qui se
            // résoudrait par rapport au domaine du FRONTEND (inexistant
            // là-bas) plutôt que celui de l'API.
            'photo' => $this->photo ? Storage::disk('public')->url($this->photo) : null,
        ];
    }
}
