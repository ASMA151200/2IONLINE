<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(
        Request $request
    ): array
    {
        return [

            'id'=>$this->id,

            'prenom'=>$this->prenom,

            'nom'=>$this->nom,

            'telephone'=>$this->telephone,

            'email'=>$this->email,

            'photo'=>$this->photo,

            'role'=>$this->role,

            'created_at'=>$this->created_at
        ];
    }
}